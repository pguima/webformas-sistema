<?php

namespace App\Livewire\Clients;

use App\Models\Web;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class WebAudit extends Component
{
    public int $webId;

    public ?Web $web = null;

    public bool $loading = false;

    public ?string $errorMessage = null;

    public ?array $general = null;

    /** @var array<int, array<string, mixed>> */
    public array $images = [];

    /** @var array<int, array<string, mixed>> */
    public array $pages = [];

    // --- Image counters ---
    public int $totalImages = 0;

    public int $imagesOk = 0;

    public int $imagesError = 0;

    public int $imagesNotWebp = 0;

    public int $imagesWithoutAlt = 0;

    public int $imagesWithoutDimensions = 0;

    public int $imagesLarge = 0;

    // --- Page / SEO counters ---
    public int $pagesWithoutMetaDescription = 0;

    public int $pagesWithoutMetaKeywords = 0;

    public int $pagesWithoutH1 = 0;

    public int $pagesWithoutTitleTag = 0;

    public int $pagesDescriptionTooLong = 0;

    public int $pagesDescriptionTooShort = 0;

    public int $pagesDuplicateDescription = 0;

    public int $pagesHtmlFetched = 0;

    // --- WordPress info ---
    public int $inactivePlugins = 0;

    public bool $sslEnabled = false;

    public ?array $pageSpeed = null;

    public array $pageSpeedErrors = [];

    public ?string $pageSpeedStrategy = 'mobile';

    public ?string $pageSpeedLastRunAt = null;

    /** @var array<string, string> src => alt coletados do HTML das páginas */
    private array $htmlImageAlts = [];

    /** @var array<string, list<array{url: string, title: string}>> src => páginas onde aparece */
    private array $htmlImagePages = [];

    /** @var array<string, list<array{url: string, title: string}>> image_url => páginas (público para o blade) */
    public array $imagePageMap = [];

    /**
     * Namespaces/recursos detectados no /wp-json
     *
     * @var array<int, string>
     */
    public array $wpNamespaces = [];

    public function mount(int $webId): void
    {
        $this->webId = $webId;
        $this->loadAudit();
    }

    private function normalizeBaseUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $trimmed = trim($url);
        if ($trimmed === '') {
            return null;
        }

        if (!Str::startsWith($trimmed, ['http://', 'https://'])) {
            $trimmed = 'https://' . $trimmed;
        }

        return rtrim($trimmed, '/');
    }

    private function wpApiUrl(string $baseUrl, string $path): string
    {
        return $baseUrl . '/wp-json/' . ltrim($path, '/');
    }

    public function loadAudit(): void
    {
        @set_time_limit(180);

        $this->loading = true;
        $this->errorMessage = null;
        $this->general = null;
        $this->images = [];
        $this->pages = [];
        $this->pageSpeed = null;
        $this->pageSpeedErrors = [];
        $this->wpNamespaces = [];
        $this->htmlImageAlts = [];
        $this->htmlImagePages = [];
        $this->imagePageMap = [];

        $this->totalImages = 0;
        $this->imagesOk = 0;
        $this->imagesError = 0;
        $this->imagesNotWebp = 0;
        $this->imagesWithoutAlt = 0;
        $this->imagesWithoutDimensions = 0;
        $this->imagesLarge = 0;

        $this->pagesWithoutMetaDescription = 0;
        $this->pagesWithoutMetaKeywords = 0;
        $this->pagesWithoutH1 = 0;
        $this->pagesWithoutTitleTag = 0;
        $this->pagesDescriptionTooLong = 0;
        $this->pagesDescriptionTooShort = 0;
        $this->pagesDuplicateDescription = 0;
        $this->pagesHtmlFetched = 0;

        $this->inactivePlugins = 0;
        $this->sslEnabled = false;

        try {
            $this->web = Web::query()->with(['client:id,name'])->findOrFail($this->webId);

            $baseUrl = $this->normalizeBaseUrl($this->web->url);
            if (!$baseUrl) {
                $this->errorMessage = 'URL do site inválida.';
                return;
            }

            $this->general = $this->fetchGeneral($baseUrl);
            $this->images = $this->fetchImages($baseUrl);
            $this->pages = $this->fetchPages($baseUrl);
            $this->enrichImageAltsFromHtml();
            $this->buildImagePageMap();

            $this->computeCounters();
        } catch (RequestException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    private function httpGetJson(string $url, array $query = []): array
    {
        $response = Http::timeout(20)
            ->acceptJson()
            ->get($url, $query);

        $response->throw();

        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    private function fetchPageHtml(string $url): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; WebAudit/1.0)'])
                ->get($url);

            return $response->successful() ? $response->body() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Faz parse do HTML e retorna meta description, meta keywords, mapa de alt de imagens,
     * title tag, primeiro H1, URL canonical e hint de versão do WordPress.
     *
     * @return array{meta_description: string|null, meta_keywords: string|null, image_alts: array<string, string>, title_tag: string|null, h1: string|null, canonical: string|null, wp_version_hint: string|null}
     */
    private function parseHtmlData(string $html): array
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8" ?>' . $html);
        libxml_clear_errors();

        $description = null;
        $keywords = null;
        $imageAlts = [];
        $wpVersionHint = null;

        /** @var \DOMElement $meta */
        foreach ($dom->getElementsByTagName('meta') as $meta) {
            $name = strtolower($meta->getAttribute('name'));
            $content = $meta->getAttribute('content');

            if ($name === 'description' && $description === null && trim($content) !== '') {
                $description = $content;
            }

            if ($name === 'keywords' && $keywords === null && trim($content) !== '') {
                $keywords = $content;
            }

            if ($name === 'generator' && $wpVersionHint === null) {
                if (preg_match('/WordPress\s+([\d.]+)/i', $content, $matches)) {
                    $wpVersionHint = $matches[1];
                }
            }
        }

        $allImageSrcs = [];

        /** @var \DOMElement $img */
        foreach ($dom->getElementsByTagName('img') as $img) {
            $src = $img->getAttribute('src');
            $alt = $img->getAttribute('alt');
            if ($src !== '') {
                $allImageSrcs[] = $src;
                if ($alt !== '') {
                    $imageAlts[$src] = $alt;
                }
            }
        }

        // Title tag
        $titleTag = null;
        $titleNodes = $dom->getElementsByTagName('title');
        if ($titleNodes->length > 0) {
            $text = trim($titleNodes->item(0)->textContent);
            if ($text !== '') {
                $titleTag = $text;
            }
        }

        // First H1
        $h1 = null;
        $h1Nodes = $dom->getElementsByTagName('h1');
        if ($h1Nodes->length > 0) {
            $text = trim($h1Nodes->item(0)->textContent);
            if ($text !== '') {
                $h1 = $text;
            }
        }

        // Canonical
        $canonical = null;
        /** @var \DOMElement $link */
        foreach ($dom->getElementsByTagName('link') as $link) {
            if (strtolower($link->getAttribute('rel')) === 'canonical') {
                $href = $link->getAttribute('href');
                if ($href !== '') {
                    $canonical = $href;
                    break;
                }
            }
        }

        return [
            'meta_description' => $description,
            'meta_keywords' => $keywords,
            'image_alts' => $imageAlts,
            'image_srcs' => $allImageSrcs,
            'title_tag' => $titleTag,
            'h1' => $h1,
            'canonical' => $canonical,
            'wp_version_hint' => $wpVersionHint,
        ];
    }

    private function fetchGeneral(string $baseUrl): array
    {
        $general = [
            'url' => $baseUrl,
            'wp_version' => null,
            'active_theme' => null,
            'plugins' => null,
            'notes' => [],
        ];

        try {
            $root = $this->httpGetJson($this->wpApiUrl($baseUrl, ''));

            if (isset($root['namespaces']) && is_array($root['namespaces'])) {
                $this->wpNamespaces = array_values(array_filter(array_map(function ($n) {
                    return is_string($n) ? $n : null;
                }, $root['namespaces'])));
            }

            $general['wp'] = [
                'name' => $root['name'] ?? null,
                'description' => $root['description'] ?? null,
                'url' => $root['url'] ?? null,
                'home' => $root['home'] ?? null,
            ];

            $general['notes'][] = 'WP Version / tema / plugins podem não estar disponíveis publicamente via REST API.';
        } catch (\Throwable $e) {
            $general['notes'][] = 'Falha ao ler /wp-json: ' . $e->getMessage();
        }

        // Detecta versão do WP via meta generator na homepage
        try {
            $html = $this->fetchPageHtml($baseUrl);
            if ($html !== null) {
                $htmlData = $this->parseHtmlData($html);
                if ($htmlData['wp_version_hint'] !== null) {
                    $general['wp_version'] = $htmlData['wp_version_hint'];
                }
            }
        } catch (\Throwable) {
            // ignore
        }

        // Best-effort: plugins (normalmente requer autenticação)
        try {
            $plugins = $this->httpGetJson($this->wpApiUrl($baseUrl, 'wp/v2/plugins'), ['per_page' => 100]);
            if (!empty($plugins)) {
                $general['plugins'] = array_map(function ($p) {
                    return [
                        'name' => $p['name'] ?? null,
                        'status' => $p['status'] ?? null,
                        'plugin' => $p['plugin'] ?? null,
                        'version' => $p['version'] ?? null,
                    ];
                }, $plugins);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Best-effort: theme (normalmente requer plugin/endpoint específico)
        try {
            $themes = $this->httpGetJson($this->wpApiUrl($baseUrl, 'wp/v2/themes'), ['per_page' => 100]);
            if (is_array($themes) && !empty($themes)) {
                $active = collect($themes)->first(function ($t) {
                    return (bool) ($t['status'] ?? null) === true || ($t['active'] ?? false) === true;
                });
                $general['active_theme'] = $active['name'] ?? null;
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return $general;
    }

    private function hasNamespace(string $prefix): bool
    {
        foreach ($this->wpNamespaces as $ns) {
            if (str_starts_with($ns, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function extractFirstString(array $data, array $needleKeys): ?string
    {
        $stack = [$data];

        while (!empty($stack)) {
            $current = array_pop($stack);

            foreach ($needleKeys as $k) {
                if (array_key_exists($k, $current)) {
                    $v = $current[$k];
                    if (is_string($v) && trim($v) !== '') {
                        return $v;
                    }
                }
            }

            foreach ($current as $v) {
                if (is_array($v)) {
                    $stack[] = $v;
                }
            }
        }

        return null;
    }

    private function fetchSeoPressMetaForPost(string $baseUrl, int $postId): array
    {
        if (!$this->hasNamespace('seopress/v1')) {
            return ['meta_description' => null, 'meta_keywords' => null];
        }

        try {
            $json = $this->httpGetJson($this->wpApiUrl($baseUrl, 'seopress/v1/posts/' . $postId));
            if (empty($json)) {
                return ['meta_description' => null, 'meta_keywords' => null];
            }

            $description = $this->extractFirstString($json, [
                'meta_desc',
                'metadesc',
                'meta_description',
                'description',
                'seo_desc',
            ]);

            $keywords = $this->extractFirstString($json, [
                'meta_key',
                'metakey',
                'meta_keywords',
                'keywords',
                'seo_keywords',
            ]);

            return [
                'meta_description' => $description,
                'meta_keywords' => $keywords,
            ];
        } catch (\Throwable $e) {
            return ['meta_description' => null, 'meta_keywords' => null];
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchImages(string $baseUrl): array
    {
        $out = [];

        $page = 1;
        $maxPages = 10;

        while ($page <= $maxPages) {
            $items = $this->httpGetJson($this->wpApiUrl($baseUrl, 'wp/v2/media'), [
                'per_page' => 100,
                'page' => $page,
                'media_type' => 'image',
            ]);

            if (empty($items)) {
                break;
            }

            foreach ($items as $m) {
                $url = (string) ($m['source_url'] ?? '');
                $ext = $url ? strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION)) : '';

                $width = $m['media_details']['width'] ?? null;
                $height = $m['media_details']['height'] ?? null;
                $alt = $m['alt_text'] ?? null;
                $filesize = $m['media_details']['filesize'] ?? null;

                $out[] = [
                    'id' => $m['id'] ?? null,
                    'url' => $url ?: null,
                    'ext' => $ext ?: null,
                    'width' => $width,
                    'height' => $height,
                    'alt_text' => is_string($alt) ? $alt : null,
                    'filesize' => is_numeric($filesize) ? (int) $filesize : null,
                ];
            }

            if (count($items) < 100) {
                break;
            }

            $page++;
        }

        return $out;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchPages(string $baseUrl): array
    {
        $out = [];

        $page = 1;
        $maxPages = 10;
        $htmlFetchCount = 0;
        $maxHtmlFetch = 30;

        while ($page <= $maxPages) {
            $items = $this->httpGetJson($this->wpApiUrl($baseUrl, 'wp/v2/pages'), [
                'per_page' => 100,
                'page' => $page,
                'context' => 'view',
            ]);

            if (empty($items)) {
                break;
            }

            foreach ($items as $p) {
                $id = $p['id'] ?? null;
                $title = $p['title']['rendered'] ?? null;
                $slug = $p['slug'] ?? null;
                $link = $p['link'] ?? null;
                $status = $p['status'] ?? null;

                $metaDescription = null;
                $metaKeywords = null;
                $h1 = null;
                $titleTag = null;
                $canonical = null;
                $htmlFetched = false;

                if (isset($p['yoast_head_json']) && is_array($p['yoast_head_json'])) {
                    $metaDescription = $p['yoast_head_json']['description'] ?? null;
                    // Yoast removeu suporte a keywords — campo sempre null
                }

                $needsDesc = !is_string($metaDescription) || trim($metaDescription) === '';
                $needsKw = !is_string($metaKeywords) || trim($metaKeywords) === '';

                if (($needsDesc || $needsKw) && is_int($id)) {
                    $seopress = $this->fetchSeoPressMetaForPost($baseUrl, $id);
                    if ($needsDesc) {
                        $metaDescription = $seopress['meta_description'];
                    }
                    if ($needsKw) {
                        $metaKeywords = $seopress['meta_keywords'];
                    }
                }

                // Fallback: parse do HTML da página (também extrai H1, title tag, canonical)
                $needsDesc = !is_string($metaDescription) || trim($metaDescription) === '';
                $needsKw = !is_string($metaKeywords) || trim($metaKeywords) === '';

                // Sempre faz fetch das primeiras $maxHtmlFetch páginas (para obter H1, title, canonical, alts)
                if (is_string($link) && $htmlFetchCount < $maxHtmlFetch) {
                    $htmlFetchCount++;
                    $htmlFetched = true;
                    $html = $this->fetchPageHtml($link);
                    if ($html !== null) {
                        $htmlData = $this->parseHtmlData($html);

                        if ($needsDesc) {
                            $metaDescription = $htmlData['meta_description'];
                        }
                        if ($needsKw) {
                            $metaKeywords = $htmlData['meta_keywords'];
                        }

                        $h1 = $htmlData['h1'];
                        $titleTag = $htmlData['title_tag'];
                        $canonical = $htmlData['canonical'];

                        foreach ($htmlData['image_alts'] as $src => $alt) {
                            $this->htmlImageAlts[$src] = $alt;
                        }

                        $pageTitle = is_string($title) ? strip_tags($title) : ($link ?? '');
                        foreach ($htmlData['image_srcs'] as $src) {
                            $this->htmlImagePages[$src][] = ['url' => $link, 'title' => $pageTitle];
                        }
                    }
                } elseif (is_string($link) && ($needsDesc || $needsKw)) {
                    // Além do limite: só faz fetch se ainda precisa de SEO data
                    $htmlFetchCount++;
                    $htmlFetched = true;
                    $html = $this->fetchPageHtml($link);
                    if ($html !== null) {
                        $htmlData = $this->parseHtmlData($html);

                        if ($needsDesc) {
                            $metaDescription = $htmlData['meta_description'];
                        }
                        if ($needsKw) {
                            $metaKeywords = $htmlData['meta_keywords'];
                        }

                        $h1 = $htmlData['h1'];
                        $titleTag = $htmlData['title_tag'];
                        $canonical = $htmlData['canonical'];

                        $pageTitle = is_string($title) ? strip_tags($title) : ($link ?? '');
                        foreach ($htmlData['image_srcs'] as $src) {
                            $this->htmlImagePages[$src][] = ['url' => $link, 'title' => $pageTitle];
                        }
                    }
                }

                $out[] = [
                    'id' => $id,
                    'title' => is_string($title) ? strip_tags($title) : null,
                    'slug' => is_string($slug) ? $slug : null,
                    'url' => is_string($link) ? $link : null,
                    'status' => is_string($status) ? $status : null,
                    'meta_description' => is_string($metaDescription) ? $metaDescription : null,
                    'meta_keywords' => is_string($metaKeywords) ? $metaKeywords : null,
                    'h1' => $h1,
                    'title_tag' => $titleTag,
                    'canonical' => $canonical,
                    'html_fetched' => $htmlFetched,
                ];
            }

            if (count($items) < 100) {
                break;
            }

            $page++;
        }

        return $out;
    }

    private function enrichImageAltsFromHtml(): void
    {
        if (empty($this->htmlImageAlts)) {
            return;
        }

        foreach ($this->images as &$img) {
            if (trim((string) ($img['alt_text'] ?? '')) !== '') {
                continue;
            }

            $url = (string) ($img['url'] ?? '');
            if ($url === '') {
                continue;
            }

            // Tentativa 1: URL exata
            if (isset($this->htmlImageAlts[$url])) {
                $img['alt_text'] = $this->htmlImageAlts[$url];
                continue;
            }

            // Tentativa 2: ignora sufixos de tamanho do WordPress (ex: -300x200)
            $normalized = preg_replace('/-\d+x\d+(\.[a-z]+)$/i', '$1', $url) ?? $url;
            foreach ($this->htmlImageAlts as $src => $alt) {
                $normalizedSrc = preg_replace('/-\d+x\d+(\.[a-z]+)$/i', '$1', $src) ?? $src;
                if ($normalizedSrc === $normalized) {
                    $img['alt_text'] = $alt;
                    break;
                }
            }
        }

        unset($img);
    }

    private function buildImagePageMap(): void
    {
        if (empty($this->htmlImagePages)) {
            return;
        }

        foreach ($this->images as $img) {
            $url = (string) ($img['url'] ?? '');
            if ($url === '') {
                continue;
            }

            $pages = [];

            if (isset($this->htmlImagePages[$url])) {
                $pages = $this->htmlImagePages[$url];
            }

            if (empty($pages)) {
                $normalized = preg_replace('/-\d+x\d+(\.[a-z]+)$/i', '$1', $url) ?? $url;
                foreach ($this->htmlImagePages as $src => $srcPages) {
                    $normalizedSrc = preg_replace('/-\d+x\d+(\.[a-z]+)$/i', '$1', $src) ?? $src;
                    if ($normalizedSrc === $normalized) {
                        $pages = array_merge($pages, $srcPages);
                    }
                }
            }

            // Deduplica por URL
            $seen = [];
            $unique = [];
            foreach ($pages as $p) {
                if (!in_array($p['url'], $seen, true)) {
                    $seen[] = $p['url'];
                    $unique[] = $p;
                }
            }

            if (!empty($unique)) {
                $this->imagePageMap[$url] = $unique;
            }
        }
    }

    private function computeCounters(): void
    {
        // SSL check
        $url = (string) ($this->general['url'] ?? '');
        $this->sslEnabled = str_starts_with($url, 'https://');

        // Plugins inativos
        if (is_array($this->general['plugins'] ?? null)) {
            foreach ($this->general['plugins'] as $plugin) {
                if (($plugin['status'] ?? 'inactive') !== 'active') {
                    $this->inactivePlugins++;
                }
            }
        }

        // Contadores de imagens
        $this->totalImages = count($this->images);
        $ok = 0;
        $err = 0;
        $notWebp = 0;
        $withoutAlt = 0;
        $withoutDimensions = 0;
        $large = 0;

        foreach ($this->images as $img) {
            $ext = strtolower((string) ($img['ext'] ?? ''));
            $alt = trim((string) ($img['alt_text'] ?? ''));
            $width = $img['width'] ?? null;
            $height = $img['height'] ?? null;
            $filesize = $img['filesize'] ?? null;

            $hasWebp = $ext === 'webp';
            $hasAlt = $alt !== '';
            $hasDimensions = $width !== null && $height !== null;

            if (!$hasWebp) {
                $notWebp++;
            }
            if (!$hasAlt) {
                $withoutAlt++;
            }
            if (!$hasDimensions) {
                $withoutDimensions++;
            }
            if ($filesize !== null && $filesize > 500000) {
                $large++;
            }

            if ($hasWebp && $hasAlt) {
                $ok++;
            } else {
                $err++;
            }
        }

        $this->imagesOk = $ok;
        $this->imagesError = $err;
        $this->imagesNotWebp = $notWebp;
        $this->imagesWithoutAlt = $withoutAlt;
        $this->imagesWithoutDimensions = $withoutDimensions;
        $this->imagesLarge = $large;

        // Contadores de páginas
        $pagesNoDesc = 0;
        $pagesNoKeywords = 0;
        $pagesNoH1 = 0;
        $pagesNoTitleTag = 0;
        $pagesDescTooLong = 0;
        $pagesDescTooShort = 0;
        $htmlFetched = 0;

        $descriptionCounts = [];

        foreach ($this->pages as $p) {
            $desc = trim((string) ($p['meta_description'] ?? ''));
            $keywords = trim((string) ($p['meta_keywords'] ?? ''));
            $fetched = (bool) ($p['html_fetched'] ?? false);

            if ($fetched) {
                $htmlFetched++;
            }

            if ($desc === '') {
                $pagesNoDesc++;
            } else {
                $len = mb_strlen($desc);
                if ($len > 160) {
                    $pagesDescTooLong++;
                } elseif ($len < 50) {
                    $pagesDescTooShort++;
                }
                $descriptionCounts[$desc] = ($descriptionCounts[$desc] ?? 0) + 1;
            }

            if ($keywords === '') {
                $pagesNoKeywords++;
            }

            // H1 e title_tag só são confiáveis quando o HTML foi buscado
            if ($fetched) {
                $h1 = trim((string) ($p['h1'] ?? ''));
                $titleTag = trim((string) ($p['title_tag'] ?? ''));

                if ($h1 === '') {
                    $pagesNoH1++;
                }
                if ($titleTag === '') {
                    $pagesNoTitleTag++;
                }
            }
        }

        // Páginas com description duplicada (conta cada página afetada)
        $duplicateDescs = 0;
        foreach ($descriptionCounts as $count) {
            if ($count > 1) {
                $duplicateDescs += $count;
            }
        }

        $this->pagesWithoutMetaDescription = $pagesNoDesc;
        $this->pagesWithoutMetaKeywords = $pagesNoKeywords;
        $this->pagesWithoutH1 = $pagesNoH1;
        $this->pagesWithoutTitleTag = $pagesNoTitleTag;
        $this->pagesDescriptionTooLong = $pagesDescTooLong;
        $this->pagesDescriptionTooShort = $pagesDescTooShort;
        $this->pagesDuplicateDescription = $duplicateDescs;
        $this->pagesHtmlFetched = $htmlFetched;
    }

    private function baseUrlOrNull(): ?string
    {
        return $this->normalizeBaseUrl($this->web?->url);
    }

    private function buildPageSpeedUrl(string $baseUrl, string $strategy): string
    {
        $key = config('services.pagespeed.key');

        $categories = ['performance', 'seo', 'accessibility', 'best-practices'];

        $query = [
            'url' => $baseUrl,
            'strategy' => $strategy,
        ];

        if ($key) {
            $query['key'] = $key;
        }

        $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        foreach ($categories as $category) {
            $queryString .= '&category=' . rawurlencode($category);
        }

        return 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?' . $queryString;
    }

    private function parsePageSpeedResponse(array $json): array
    {
        $lhr = $json['lighthouseResult'] ?? [];
        $categories = $lhr['categories'] ?? [];
        $audits = $lhr['audits'] ?? [];

        $score = function (string $key) use ($categories): ?float {
            $v = $categories[$key]['score'] ?? null;
            return is_numeric($v) ? (float) $v : null;
        };

        $auditNumeric = function (string $key, string $field = 'numericValue') use ($audits): ?float {
            $v = $audits[$key][$field] ?? null;
            return is_numeric($v) ? (float) $v : null;
        };

        return [
            'scores' => [
                'performance' => $score('performance'),
                'seo' => $score('seo'),
                'accessibility' => $score('accessibility'),
                'best_practices' => $score('best-practices'),
            ],
            'metrics' => [
                'lcp_ms' => $auditNumeric('largest-contentful-paint'),
                'cls' => $auditNumeric('cumulative-layout-shift'),
                'ttfb_ms' => $auditNumeric('server-response-time'),
                'speed_index_ms' => $auditNumeric('speed-index'),
            ],
        ];
    }

    public function analyzePageSpeed(): void
    {
        @set_time_limit(180);

        $baseUrl = $this->baseUrlOrNull();
        if (!$baseUrl) {
            $this->dispatch('notify', message: 'URL do site inválida.', variant: 'danger', title: 'Erro');
            return;
        }

        $this->pageSpeed = null;
        $this->pageSpeedErrors = [];

        $strategies = ['mobile', 'desktop'];
        $urls = [];
        foreach ($strategies as $strategy) {
            $urls[$strategy] = $this->buildPageSpeedUrl($baseUrl, $strategy);
        }

        $responses = Http::pool(function ($pool) use ($urls) {
            $out = [];
            foreach ($urls as $strategy => $url) {
                $out[$strategy] = $pool
                    ->as($strategy)
                    ->timeout(120)
                    ->acceptJson()
                    ->get($url);
            }
            return $out;
        });

        $out = [];
        foreach ($strategies as $strategy) {
            try {
                $response = $responses[$strategy] ?? null;
                if (!$response) {
                    continue;
                }

                $response->throw();

                $json = $response->json();
                if (!is_array($json)) {
                    $json = [];
                }

                $out[$strategy] = $this->parsePageSpeedResponse($json);
            } catch (RequestException $e) {
                $response = $e->response;
                $status = $response?->status();
                $body = $response?->body();

                $details = $e->getMessage();
                if ($status) {
                    $details .= ' (HTTP ' . $status . ')';
                }
                if (is_string($body) && trim($body) !== '') {
                    $details .= ' - ' . Str::limit(trim($body), 500);
                }

                $this->pageSpeedErrors[$strategy] = $details;
                $this->dispatch('notify', message: '[' . $strategy . '] ' . $details, variant: 'danger', title: 'Erro');
            } catch (\Throwable $e) {
                $this->pageSpeedErrors[$strategy] = $e->getMessage();
                $this->dispatch('notify', message: '[' . $strategy . '] ' . $e->getMessage(), variant: 'danger', title: 'Erro');
            }
        }

        $this->pageSpeed = !empty($out) ? $out : null;
        if ($this->pageSpeed) {
            $this->pageSpeedLastRunAt = now()->format('d/m/Y H:i');

            $desktopScores = $this->pageSpeed['desktop']['scores'] ?? null;
            if (is_array($desktopScores)) {
                $toInt = function ($value): ?int {
                    if (!is_numeric($value)) {
                        return null;
                    }

                    $v = (float) $value;
                    if ($v <= 1) {
                        $v *= 100;
                    }

                    $v = (int) round($v);
                    if ($v < 0) {
                        $v = 0;
                    }
                    if ($v > 100) {
                        $v = 100;
                    }

                    return $v;
                };

                try {
                    $this->web?->update([
                        'performance' => $toInt($desktopScores['performance'] ?? null),
                        'seo' => $toInt($desktopScores['seo'] ?? null),
                        'accessibility' => $toInt($desktopScores['accessibility'] ?? null),
                        'best_practices' => $toInt($desktopScores['best_practices'] ?? null),
                        'pagespeed_last_checked_at' => now(),
                    ]);

                    $this->dispatch('client-webs-refresh');
                } catch (\Throwable $e) {
                    $this->dispatch('notify', message: $e->getMessage(), variant: 'danger', title: 'Erro');
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.clients.web-audit');
    }
}
