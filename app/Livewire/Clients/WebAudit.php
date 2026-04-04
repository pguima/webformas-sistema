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

    // --- WordPress Security ---
    /** @var array<int, array<string, mixed>> */
    public array $wpSecurityChecks = [];

    public bool $wpSecurityLoading = false;

    public ?string $wpSecurityError = null;

    public int $wpSecurityScore = 0;

    // --- Sitemap & Robots ---
    /** @var array<string, mixed>|null */
    public ?array $sitemapData = null;

    /** @var array<string, mixed>|null */
    public ?array $robotsData = null;

    public bool $sitemapLoading = false;

    public ?string $sitemapError = null;

    // --- AI Content Analysis ---
    /** @var array<string, mixed>|null */
    public ?array $aiAnalysis = null;

    public bool $aiLoading = false;

    public ?string $aiError = null;

    // --- GEO (Generative Engine Optimization) ---
    /** @var array<int, array<string, mixed>> */
    public array $geoChecks = [];

    public bool $geoLoading = false;

    public ?string $geoError = null;

    public int $geoScore = 0;

    // --- Security Headers ---
    /** @var array<int, array<string, mixed>> */
    public array $securityChecks = [];

    public bool $securityLoading = false;

    public ?string $securityError = null;

    public int $securityScore = 0;

    // --- Schema Markup ---
    /** @var array<int, array<string, mixed>> */
    public array $schemaItems = [];

    public bool $schemaLoading = false;

    public ?string $schemaError = null;

    /** @var array<string, mixed> Open Graph tags */
    public array $ogTags = [];

    /** @var array<string, mixed> Twitter Card tags */
    public array $twitterTags = [];

    // --- Link crawler ---
    /** @var array<int, array<string, mixed>> */
    public array $links = [];

    public bool $linksLoading = false;

    public ?string $linksError = null;

    /** @var array<string, bool> href => checked (private accumulator during audit) */
    private array $htmlLinks = [];

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
        $this->wpSecurityChecks = [];
        $this->wpSecurityLoading = false;
        $this->wpSecurityError = null;
        $this->wpSecurityScore = 0;

        $this->sitemapData = null;
        $this->robotsData = null;
        $this->sitemapLoading = false;
        $this->sitemapError = null;

        $this->aiAnalysis = null;
        $this->aiLoading = false;
        $this->aiError = null;

        $this->geoChecks = [];
        $this->geoLoading = false;
        $this->geoError = null;
        $this->geoScore = 0;

        $this->securityChecks = [];
        $this->securityLoading = false;
        $this->securityError = null;
        $this->securityScore = 0;

        $this->schemaItems = [];
        $this->schemaLoading = false;
        $this->schemaError = null;
        $this->ogTags = [];
        $this->twitterTags = [];

        $this->links = [];
        $this->linksLoading = false;
        $this->linksError = null;
        $this->htmlLinks = [];
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

        // Extract all <a href> links
        $links = [];
        /** @var \DOMElement $anchor */
        foreach ($dom->getElementsByTagName('a') as $anchor) {
            $href = trim($anchor->getAttribute('href'));
            if ($href === '' || str_starts_with($href, '#') || str_starts_with($href, 'javascript:') || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:')) {
                continue;
            }
            $links[] = $href;
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
            'links' => $links,
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
                        foreach ($htmlData['links'] ?? [] as $href) {
                            $this->htmlLinks[$href] = true;
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
                        foreach ($htmlData['links'] ?? [] as $href) {
                            $this->htmlLinks[$href] = true;
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

    public function checkWpSecurity(): void
    {
        @set_time_limit(180);

        $this->wpSecurityLoading = true;
        $this->wpSecurityError = null;
        $this->wpSecurityChecks = [];
        $this->wpSecurityScore = 0;

        $baseUrl = $this->baseUrlOrNull();
        if (!$baseUrl) {
            $this->wpSecurityError = 'URL do site inválida.';
            $this->wpSecurityLoading = false;
            return;
        }

        $checks = [];
        $base = rtrim($baseUrl, '/');

        // Helper: check URL, return status
        $checkUrl = function (string $url, string $method = 'get'): int {
            try {
                $r = Http::timeout(8)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->$method($url);
                return $r->status();
            } catch (\Throwable) {
                return 0;
            }
        };

        // 1. readme.html — exposes WP version
        $status = $checkUrl("{$base}/readme.html");
        $exposed = $status === 200;
        $checks[] = [
            'name'     => 'readme.html exposto',
            'status'   => $exposed ? 'fail' : 'pass',
            'value'    => $exposed ? "HTTP {$status} — expõe versão do WordPress" : 'Bloqueado',
            'severity' => 'medium',
            'tip'      => 'Bloqueie o acesso ao readme.html via .htaccess ou renomeie o arquivo.',
        ];

        // 2. license.txt — exposes WP version
        $status = $checkUrl("{$base}/license.txt");
        $exposed = $status === 200;
        $checks[] = [
            'name'     => 'license.txt exposto',
            'status'   => $exposed ? 'fail' : 'pass',
            'value'    => $exposed ? "HTTP {$status} — expõe versão do WordPress" : 'Bloqueado',
            'severity' => 'low',
            'tip'      => 'Bloqueie o acesso ao license.txt via .htaccess.',
        ];

        // 3. wp-config.php backup files
        $backupFiles = ['wp-config.php.bak', 'wp-config.bak', 'wp-config.php.old', 'wp-config.php~'];
        $foundBackup = null;
        foreach ($backupFiles as $bf) {
            $s = $checkUrl("{$base}/{$bf}");
            if ($s === 200) {
                $foundBackup = $bf;
                break;
            }
        }
        $checks[] = [
            'name'     => 'wp-config.php backup exposto',
            'status'   => $foundBackup ? 'fail' : 'pass',
            'value'    => $foundBackup ? "CRÍTICO: {$foundBackup} acessível publicamente!" : 'Nenhum backup encontrado',
            'severity' => 'critical',
            'tip'      => 'Remova ou bloqueie arquivos de backup do wp-config.php imediatamente.',
        ];

        // 4. wp-login.php exposed (not a problem itself, but check for rate limiting)
        $status = $checkUrl("{$base}/wp-login.php");
        $loginExposed = $status === 200;
        $checks[] = [
            'name'     => 'wp-login.php exposto sem proteção',
            'status'   => $loginExposed ? 'warn' : 'pass',
            'value'    => $loginExposed ? 'Acessível publicamente (verifique se há rate limiting)' : 'Bloqueado ou redirecionado',
            'severity' => 'medium',
            'tip'      => 'Adicione limitação de tentativas de login (plugin Limit Login Attempts) ou mova para URL customizada.',
        ];

        // 5. User enumeration via ?author=1
        try {
            $r = Http::timeout(8)->withOptions(['allow_redirects' => false])->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get("{$base}/?author=1");
            $location = $r->header('Location') ?? '';
            $enumerable = $r->status() === 301 && preg_match('/\/author\/[^\/]+\/?$/', $location);
        } catch (\Throwable) {
            $enumerable = false;
        }
        $checks[] = [
            'name'     => 'Enumeração de usuários (?author=1)',
            'status'   => $enumerable ? 'fail' : 'pass',
            'value'    => $enumerable ? 'Usuários enumeráveis — nome de usuário pode ser descoberto' : 'Protegido',
            'severity' => 'high',
            'tip'      => 'Bloqueie ?author= queries via .htaccess ou use plugin de segurança para ocultar nomes de usuário.',
        ];

        // 6. xmlrpc.php (already checked in security, but WP-specific context)
        $status = $checkUrl("{$base}/xmlrpc.php");
        $xmlrpcExposed = in_array($status, [200, 405], true);
        $checks[] = [
            'name'     => 'xmlrpc.php exposto',
            'status'   => $xmlrpcExposed ? 'fail' : 'pass',
            'value'    => $xmlrpcExposed ? "HTTP {$status} — vetor de ataques de força bruta e DDoS" : 'Bloqueado',
            'severity' => 'high',
            'tip'      => 'Desabilite o xmlrpc.php via plugin ou adicione: <Files xmlrpc.php><Order Deny,Allow><Deny from all></Files> no .htaccess.',
        ];

        // 7. WordPress REST API user listing
        try {
            $r = Http::timeout(8)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get("{$base}/wp-json/wp/v2/users");
            $usersExposed = $r->successful() && is_array($r->json()) && !empty($r->json());
        } catch (\Throwable) {
            $usersExposed = false;
        }
        $checks[] = [
            'name'     => 'REST API expõe lista de usuários',
            'status'   => $usersExposed ? 'fail' : 'pass',
            'value'    => $usersExposed ? 'Usuários listados via /wp-json/wp/v2/users — expõe logins' : 'Protegido ou vazio',
            'severity' => 'high',
            'tip'      => 'Desative a listagem pública de usuários via plugin ou filtro: remove_filter("rest_endpoints", ...)',
        ];

        // 8. WP version disclosure via meta generator (already in general, repeat as security check)
        $wpVersion = $this->general['wp_version'] ?? null;
        $checks[] = [
            'name'     => 'Versão WordPress exposta (meta generator)',
            'status'   => $wpVersion ? 'warn' : 'pass',
            'value'    => $wpVersion ? "WordPress {$wpVersion} detectado na tag <meta generator>" : 'Versão não exposta',
            'severity' => 'low',
            'tip'      => 'Remova a tag meta generator com: remove_action("wp_head", "wp_generator"); no functions.php.',
        ];

        // 9. Debug mode / error output
        $html = $this->fetchPageHtml($baseUrl);
        $debugMode = false;
        if ($html) {
            $debugMode = str_contains($html, 'WP_DEBUG') || preg_match('/Fatal error|WordPress database error|on line \d+/i', $html) === 1;
        }
        $checks[] = [
            'name'     => 'WP_DEBUG ativo em produção',
            'status'   => $debugMode ? 'fail' : 'pass',
            'value'    => $debugMode ? 'Erros PHP visíveis no HTML — expõe estrutura interna' : 'Nenhum erro visível detectado',
            'severity' => 'critical',
            'tip'      => 'Defina WP_DEBUG como false no wp-config.php em produção.',
        ];

        // 10. Uploads directory indexing
        $status = $checkUrl("{$base}/wp-content/uploads/");
        $indexingEnabled = $status === 200;
        $checks[] = [
            'name'     => 'Directory listing em /wp-content/uploads/',
            'status'   => $indexingEnabled ? 'warn' : 'pass',
            'value'    => $indexingEnabled ? 'Listagem de arquivos ativa — pode expor uploads privados' : 'Protegido',
            'severity' => 'medium',
            'tip'      => 'Adicione "Options -Indexes" no .htaccess do diretório uploads.',
        ];

        $this->wpSecurityChecks = $checks;

        // Score
        $weights = ['critical' => 30, 'high' => 20, 'medium' => 15, 'low' => 5];
        $total = 0;
        $earned = 0;
        foreach ($checks as $c) {
            $w = $weights[$c['severity']] ?? 5;
            $total += $w;
            if ($c['status'] === 'pass') {
                $earned += $w;
            } elseif ($c['status'] === 'warn') {
                $earned += (int) ($w * 0.5);
            }
        }
        $this->wpSecurityScore = $total > 0 ? (int) round(($earned / $total) * 100) : 0;

        $this->wpSecurityLoading = false;
    }

    public function analyzeSitemapRobots(): void
    {
        $this->sitemapLoading = true;
        $this->sitemapError = null;
        $this->sitemapData = null;
        $this->robotsData = null;

        $baseUrl = $this->baseUrlOrNull();
        if (!$baseUrl) {
            $this->sitemapError = 'URL do site inválida.';
            $this->sitemapLoading = false;
            return;
        }

        // --- robots.txt ---
        try {
            $r = Http::timeout(10)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get(rtrim($baseUrl, '/') . '/robots.txt');
            if ($r->successful() && trim($r->body()) !== '') {
                $body = $r->body();
                $lines = array_map('trim', explode("\n", $body));
                $rules = [];
                $sitemapUrls = [];
                $currentAgent = '*';

                foreach ($lines as $line) {
                    if ($line === '' || str_starts_with($line, '#')) {
                        continue;
                    }
                    if (preg_match('/^User-agent:\s*(.+)$/i', $line, $m)) {
                        $currentAgent = trim($m[1]);
                    } elseif (preg_match('/^(Allow|Disallow):\s*(.*)$/i', $line, $m)) {
                        $rules[] = ['agent' => $currentAgent, 'directive' => strtolower($m[1]), 'path' => $m[2]];
                    } elseif (preg_match('/^Sitemap:\s*(.+)$/i', $line, $m)) {
                        $sitemapUrls[] = trim($m[1]);
                    }
                }

                $disallowAll = collect($rules)->first(fn($r) => $r['directive'] === 'disallow' && $r['path'] === '/' && $r['agent'] === '*');

                $this->robotsData = [
                    'found'        => true,
                    'content'      => Str::limit($body, 2000),
                    'rules_count'  => count($rules),
                    'sitemap_urls' => $sitemapUrls,
                    'disallow_all' => (bool) $disallowAll,
                    'issues'       => $disallowAll ? ['Disallow: / bloqueia todos os crawlers — o site não será indexado!'] : [],
                ];
            } else {
                $this->robotsData = ['found' => false, 'issues' => ['robots.txt não encontrado ou vazio.']];
            }
        } catch (\Throwable $e) {
            $this->robotsData = ['found' => false, 'issues' => ['Erro ao acessar robots.txt: ' . $e->getMessage()]];
        }

        // --- sitemap.xml ---
        $sitemapUrls = $this->robotsData['sitemap_urls'] ?? [];
        if (empty($sitemapUrls)) {
            $sitemapUrls = [rtrim($baseUrl, '/') . '/sitemap.xml', rtrim($baseUrl, '/') . '/sitemap_index.xml'];
        }

        $foundSitemap = null;
        foreach ($sitemapUrls as $sitemapUrl) {
            try {
                $r = Http::timeout(15)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($sitemapUrl);
                if ($r->successful()) {
                    $foundSitemap = ['url' => $sitemapUrl, 'body' => $r->body()];
                    break;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        if ($foundSitemap) {
            $body = $foundSitemap['body'];
            $urlCount = substr_count($body, '<url>');
            $isIndex = str_contains($body, '<sitemapindex') || str_contains($body, '<sitemap>');
            $sitemapFiles = [];
            if ($isIndex) {
                preg_match_all('/<loc>(.*?)<\/loc>/s', $body, $m);
                $sitemapFiles = array_slice($m[1] ?? [], 0, 10);
            }

            // Check for lastmod
            $hasLastmod = str_contains($body, '<lastmod>');
            $hasImages = str_contains($body, 'image:image') || str_contains($body, 'xmlns:image');
            $hasVideos = str_contains($body, 'video:video') || str_contains($body, 'xmlns:video');

            $issues = [];
            if (!$hasLastmod && !$isIndex) {
                $issues[] = 'URLs sem <lastmod> — buscadores podem re-crawlar desnecessariamente.';
            }

            // Sample some URLs
            preg_match_all('/<loc>(.*?)<\/loc>/s', $body, $locMatches);
            $sampleUrls = array_slice($locMatches[1] ?? [], 0, 10);

            $this->sitemapData = [
                'found'         => true,
                'url'           => $foundSitemap['url'],
                'is_index'      => $isIndex,
                'url_count'     => $urlCount,
                'sitemap_files' => $sitemapFiles,
                'has_lastmod'   => $hasLastmod,
                'has_images'    => $hasImages,
                'has_videos'    => $hasVideos,
                'sample_urls'   => $sampleUrls,
                'issues'        => $issues,
            ];
        } else {
            $this->sitemapData = [
                'found'  => false,
                'issues' => ['Nenhum sitemap.xml encontrado. Crie um e referencie no robots.txt.'],
            ];
        }

        $this->sitemapLoading = false;
    }

    public function analyzeContentWithAi(): void
    {
        @set_time_limit(120);

        $this->aiLoading = true;
        $this->aiError = null;
        $this->aiAnalysis = null;

        $apiKey = config('services.openai.key');
        $model = config('services.openai.model', 'gpt-4o-mini');

        if (!$apiKey) {
            $this->aiError = 'OPENAI_API_KEY não configurada no .env.';
            $this->aiLoading = false;
            return;
        }

        $baseUrl = $this->baseUrlOrNull();
        if (!$baseUrl) {
            $this->aiError = 'URL do site inválida.';
            $this->aiLoading = false;
            return;
        }

        // Collect content from already-fetched pages or fetch homepage
        $textContent = '';
        $pagesSample = array_slice($this->pages, 0, 5);

        if (!empty($pagesSample)) {
            foreach ($pagesSample as $page) {
                $title = $page['title'] ?? '';
                $desc = $page['meta_description'] ?? '';
                $h1 = $page['h1'] ?? '';
                if ($title || $desc || $h1) {
                    $textContent .= "Página: {$title}\nH1: {$h1}\nDescrição: {$desc}\n\n";
                }
            }
        }

        if (!$textContent) {
            $html = $this->fetchPageHtml($baseUrl);
            if ($html) {
                $dom = new \DOMDocument();
                libxml_use_internal_errors(true);
                $dom->loadHTML('<?xml encoding="UTF-8" ?>' . $html);
                libxml_clear_errors();

                // Remove scripts, styles
                foreach (['script', 'style', 'nav', 'footer', 'header'] as $tag) {
                    foreach (iterator_to_array($dom->getElementsByTagName($tag)) as $node) {
                        $node->parentNode?->removeChild($node);
                    }
                }
                $raw = $dom->textContent ?? '';
                $textContent = preg_replace('/\s+/', ' ', $raw) ?? '';
                $textContent = Str::limit(trim($textContent), 3000);
            }
        }

        if (!$textContent) {
            $this->aiError = 'Não foi possível extrair conteúdo textual do site.';
            $this->aiLoading = false;
            return;
        }

        $siteName = $this->web?->name ?? $baseUrl;
        $siteType = $this->web?->type ?? 'site';
        $objective = $this->web?->objective ?? '';

        $systemPrompt = 'Você é um especialista em SEO e marketing de conteúdo. Analise o conteúdo fornecido e retorne APENAS um JSON válido, sem markdown, sem explicações extras.';

        $userPrompt = <<<PROMPT
Analise o conteúdo do site "{$siteName}" (tipo: {$siteType}, objetivo: {$objective}).

Conteúdo das páginas principais:
{$textContent}

Retorne EXATAMENTE este JSON (sem código markdown, sem texto adicional):
{
  "resumo": "resumo do site em 2-3 frases",
  "tom_de_voz": "tom de voz detectado (ex: formal, informal, técnico, comercial)",
  "publico_alvo_estimado": "perfil estimado do público-alvo",
  "palavras_chave_detectadas": ["kw1", "kw2", "kw3", "kw4", "kw5"],
  "palavras_chave_sugeridas": ["kw1", "kw2", "kw3", "kw4", "kw5"],
  "pontos_fortes": ["ponto1", "ponto2", "ponto3"],
  "oportunidades_melhoria": ["oportunidade1", "oportunidade2", "oportunidade3"],
  "legibilidade": "nota de 1-10 com breve justificativa",
  "intenção_de_busca": "informacional | navegacional | comercial | transacional",
  "recomendacoes_conteudo": ["recomendacao1", "recomendacao2", "recomendacao3"],
  "score_qualidade": 75
}
PROMPT;

        try {
            $response = Http::timeout(60)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 1000,
                    'response_format' => ['type' => 'json_object'],
                ]);

            $response->throw();
            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? null;

            if (!$content) {
                throw new \RuntimeException('Resposta vazia da OpenAI.');
            }

            $parsed = json_decode($content, true);
            if (!is_array($parsed)) {
                throw new \RuntimeException('JSON inválido retornado pela OpenAI.');
            }

            $this->aiAnalysis = $parsed;
        } catch (RequestException $e) {
            $body = $e->response?->body();
            $this->aiError = 'Erro na API OpenAI: ' . $e->getMessage() . ($body ? ' — ' . Str::limit($body, 200) : '');
        } catch (\Throwable $e) {
            $this->aiError = $e->getMessage();
        }

        $this->aiLoading = false;
    }

    public function checkGeo(): void
    {
        $this->geoLoading = true;
        $this->geoError = null;
        $this->geoChecks = [];
        $this->geoScore = 0;

        $baseUrl = $this->baseUrlOrNull();
        if (!$baseUrl) {
            $this->geoError = 'URL do site inválida.';
            $this->geoLoading = false;
            return;
        }

        $checks = [];

        // 1. llms.txt
        try {
            $r = Http::timeout(10)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get(rtrim($baseUrl, '/') . '/llms.txt');
            $hasLlms = $r->successful() && trim($r->body()) !== '';
            $llmsContent = $hasLlms ? Str::limit(trim($r->body()), 300) : null;
        } catch (\Throwable) {
            $hasLlms = false;
            $llmsContent = null;
        }
        $checks[] = [
            'name'        => 'llms.txt',
            'description' => 'Arquivo de instrução para LLMs (similar ao robots.txt para IA)',
            'status'      => $hasLlms ? 'pass' : 'fail',
            'value'       => $hasLlms ? $llmsContent : 'Ausente',
            'weight'      => 15,
            'tip'         => 'Crie /llms.txt descrevendo o site, seus objetivos e o que a IA pode ou não referenciar.',
        ];

        // 2. robots.txt — AI crawlers
        try {
            $r = Http::timeout(10)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get(rtrim($baseUrl, '/') . '/robots.txt');
            $robotsBody = $r->successful() ? $r->body() : '';
        } catch (\Throwable) {
            $robotsBody = '';
        }

        $aiCrawlers = ['GPTBot', 'ClaudeBot', 'PerplexityBot', 'Googlebot', 'anthropic-ai', 'ChatGPT-User', 'cohere-ai'];
        $blockedCrawlers = [];
        $allowedCrawlers = [];

        foreach ($aiCrawlers as $crawler) {
            if (preg_match('/User-agent:\s*' . preg_quote($crawler, '/') . '.*?Disallow:\s*\//is', $robotsBody)) {
                $blockedCrawlers[] = $crawler;
            } else {
                $allowedCrawlers[] = $crawler;
            }
        }

        $checks[] = [
            'name'        => 'Crawlers de IA no robots.txt',
            'description' => 'Verifica se GPTBot, ClaudeBot, PerplexityBot e outros podem indexar o site',
            'status'      => empty($blockedCrawlers) ? 'pass' : 'warn',
            'value'       => empty($blockedCrawlers)
                ? 'Permitidos: ' . implode(', ', $allowedCrawlers)
                : 'Bloqueados: ' . implode(', ', $blockedCrawlers) . ' | Permitidos: ' . implode(', ', $allowedCrawlers),
            'weight'      => 20,
            'tip'         => 'Se quiser ser citado em respostas de IA, certifique-se que GPTBot, ClaudeBot e PerplexityBot não estão bloqueados.',
        ];

        // 3. Fetch homepage HTML for content analysis
        $html = $this->fetchPageHtml($baseUrl);
        $htmlData = $html ? $this->parseHtmlData($html) : null;

        // 4. FAQ / Q&A content (FAQPage schema or heading patterns)
        $hasFaqSchema = false;
        if ($html) {
            $hasFaqSchema = str_contains($html, 'FAQPage') || str_contains($html, '"@type":"FAQPage"') || str_contains($html, '"@type": "FAQPage"');
        }
        $checks[] = [
            'name'        => 'Conteúdo FAQ / Perguntas e Respostas',
            'description' => 'Conteúdo em formato Q&A é altamente citável por sistemas de IA',
            'status'      => $hasFaqSchema ? 'pass' : 'warn',
            'value'       => $hasFaqSchema ? 'FAQPage Schema detectado' : 'Nenhum FAQ Schema encontrado',
            'weight'      => 15,
            'tip'         => 'Adicione uma seção de perguntas frequentes com FAQPage Schema para aumentar a chance de ser citado.',
        ];

        // 5. Definition / structured answers (H2/H3 followed by short paragraphs)
        $hasDefinitions = false;
        if ($html) {
            // Look for patterns like "O que é..." or "Como funciona..."
            $hasDefinitions = preg_match('/<h[23][^>]*>(O que|Como|Por que|Quando|Qual|What is|How to)/i', $html) === 1;
        }
        $checks[] = [
            'name'        => 'Respostas diretas (definition blocks)',
            'description' => 'Seções com respostas diretas a perguntas são preferidas em AI Overviews',
            'status'      => $hasDefinitions ? 'pass' : 'warn',
            'value'       => $hasDefinitions ? 'Padrões de pergunta/resposta detectados nos headings' : 'Nenhum padrão detectado',
            'weight'      => 10,
            'tip'         => 'Use headings como "O que é X?" ou "Como funciona Y?" seguidos de respostas diretas e concisas.',
        ];

        // 6. Author/entity signals
        $hasAuthor = false;
        if ($html) {
            $hasAuthor = str_contains($html, '"author"') || str_contains($html, 'rel="author"') || preg_match('/\bauthor\b/i', $html) === 1;
        }
        $checks[] = [
            'name'        => 'Sinais de Autoria / E-E-A-T',
            'description' => 'Presença de informações de autor, experiência e autoridade',
            'status'      => $hasAuthor ? 'pass' : 'warn',
            'value'       => $hasAuthor ? 'Sinais de autoria detectados' : 'Sem sinais claros de autoria',
            'weight'      => 10,
            'tip'         => 'Inclua schema de Person/Organization com nome, cargo e especialidade. Adicione página "Sobre".',
        ];

        // 7. Structured data variety (more types = better for AI extraction)
        $schemaTypes = [];
        if ($html) {
            preg_match_all('/"@type"\s*:\s*"([^"]+)"/', $html, $matches);
            $schemaTypes = array_unique($matches[1] ?? []);
        }
        $hasRichSchema = count($schemaTypes) >= 2;
        $checks[] = [
            'name'        => 'Variedade de Structured Data',
            'description' => 'Múltiplos tipos de Schema ajudam sistemas de IA a entender o contexto',
            'status'      => $hasRichSchema ? 'pass' : ($schemaTypes ? 'warn' : 'fail'),
            'value'       => !empty($schemaTypes) ? implode(', ', $schemaTypes) : 'Nenhum Schema detectado',
            'weight'      => 10,
            'tip'         => 'Use pelo menos Organization + WebSite + um tipo específico (Article, Product, FAQPage, etc.).',
        ];

        // 8. HTTPS (for AI crawler trust)
        $checks[] = [
            'name'        => 'HTTPS para crawlers de IA',
            'description' => 'Sites HTTPS têm maior probabilidade de ser indexados por crawlers de IA',
            'status'      => str_starts_with($baseUrl, 'https://') ? 'pass' : 'fail',
            'value'       => str_starts_with($baseUrl, 'https://') ? 'HTTPS ativo' : 'HTTP — crawlers de IA podem ignorar',
            'weight'      => 10,
            'tip'         => 'Ative HTTPS no seu site.',
        ];

        // 9. Page description quality
        $desc = $htmlData['meta_description'] ?? null;
        $descLen = $desc ? mb_strlen(trim($desc)) : 0;
        $goodDesc = $descLen >= 120 && $descLen <= 160;
        $checks[] = [
            'name'        => 'Meta description informativa',
            'description' => 'Descrição completa ajuda IA a entender o propósito da página',
            'status'      => $goodDesc ? 'pass' : ($descLen > 0 ? 'warn' : 'fail'),
            'value'       => $desc ? "\"" . Str::limit($desc, 100) . "\" ({$descLen} chars)" : 'Ausente',
            'weight'      => 10,
            'tip'         => 'Escreva uma meta description completa entre 120-160 caracteres descrevendo claramente o conteúdo.',
        ];

        $this->geoChecks = $checks;

        // Score
        $total = array_sum(array_column($checks, 'weight'));
        $earned = 0;
        foreach ($checks as $c) {
            if ($c['status'] === 'pass') {
                $earned += $c['weight'];
            } elseif ($c['status'] === 'warn') {
                $earned += (int) ($c['weight'] * 0.5);
            }
        }
        $this->geoScore = $total > 0 ? (int) round(($earned / $total) * 100) : 0;

        $this->geoLoading = false;
    }

    public function checkSecurity(): void
    {
        $this->securityLoading = true;
        $this->securityError = null;
        $this->securityChecks = [];
        $this->securityScore = 0;

        $baseUrl = $this->baseUrlOrNull();
        if (!$baseUrl) {
            $this->securityError = 'URL do site inválida.';
            $this->securityLoading = false;
            return;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; WebAudit/1.0)'])
                ->get($baseUrl);
            $headers = array_change_key_case($response->headers(), CASE_LOWER);
        } catch (\Throwable $e) {
            $this->securityError = 'Falha ao conectar: ' . $e->getMessage();
            $this->securityLoading = false;
            return;
        }

        $getHeader = function (string $name) use ($headers): ?string {
            $val = $headers[$name] ?? $headers[strtolower($name)] ?? null;
            if (is_array($val)) {
                $val = $val[0] ?? null;
            }
            return is_string($val) ? $val : null;
        };

        $checks = [];

        // 1. HTTPS
        $checks[] = [
            'name'        => 'HTTPS',
            'description' => 'Site acessível via HTTPS (SSL/TLS)',
            'status'      => str_starts_with($baseUrl, 'https://') ? 'pass' : 'fail',
            'value'       => str_starts_with($baseUrl, 'https://') ? 'Ativo' : 'Inativo — todo tráfego é não-criptografado',
            'severity'    => 'critical',
            'tip'         => 'Instale um certificado SSL/TLS e force redirect HTTP → HTTPS.',
        ];

        // 2. HSTS
        $hsts = $getHeader('strict-transport-security');
        $checks[] = [
            'name'        => 'HSTS',
            'description' => 'HTTP Strict Transport Security',
            'status'      => $hsts ? 'pass' : 'fail',
            'value'       => $hsts ?: 'Ausente',
            'severity'    => 'high',
            'tip'         => 'Adicione: Strict-Transport-Security: max-age=31536000; includeSubDomains',
        ];

        // 3. X-Frame-Options
        $xfo = $getHeader('x-frame-options');
        $xfoValid = $xfo && preg_match('/DENY|SAMEORIGIN/i', $xfo);
        $checks[] = [
            'name'        => 'X-Frame-Options',
            'description' => 'Proteção contra Clickjacking',
            'status'      => $xfoValid ? 'pass' : ($xfo ? 'warn' : 'fail'),
            'value'       => $xfo ?: 'Ausente',
            'severity'    => 'medium',
            'tip'         => 'Adicione: X-Frame-Options: SAMEORIGIN',
        ];

        // 4. X-Content-Type-Options
        $xcto = $getHeader('x-content-type-options');
        $checks[] = [
            'name'        => 'X-Content-Type-Options',
            'description' => 'Previne MIME-type sniffing',
            'status'      => $xcto === 'nosniff' ? 'pass' : 'fail',
            'value'       => $xcto ?: 'Ausente',
            'severity'    => 'medium',
            'tip'         => 'Adicione: X-Content-Type-Options: nosniff',
        ];

        // 5. Content-Security-Policy
        $csp = $getHeader('content-security-policy');
        $checks[] = [
            'name'        => 'Content-Security-Policy',
            'description' => 'Controla recursos que o browser pode carregar (previne XSS)',
            'status'      => $csp ? 'pass' : 'fail',
            'value'       => $csp ? Str::limit($csp, 100) : 'Ausente',
            'severity'    => 'high',
            'tip'         => 'Adicione uma CSP adequada ao seu site. Ex: Content-Security-Policy: default-src \'self\'',
        ];

        // 6. Referrer-Policy
        $rp = $getHeader('referrer-policy');
        $checks[] = [
            'name'        => 'Referrer-Policy',
            'description' => 'Controla quais informações de referência são enviadas',
            'status'      => $rp ? 'pass' : 'warn',
            'value'       => $rp ?: 'Ausente (padrão do browser)',
            'severity'    => 'low',
            'tip'         => 'Adicione: Referrer-Policy: strict-origin-when-cross-origin',
        ];

        // 7. Permissions-Policy (antiga Feature-Policy)
        $pp = $getHeader('permissions-policy') ?? $getHeader('feature-policy');
        $checks[] = [
            'name'        => 'Permissions-Policy',
            'description' => 'Controla acesso a APIs do browser (câmera, microfone, geolocalização)',
            'status'      => $pp ? 'pass' : 'warn',
            'value'       => $pp ? Str::limit($pp, 80) : 'Ausente',
            'severity'    => 'low',
            'tip'         => 'Adicione: Permissions-Policy: geolocation=(), microphone=(), camera=()',
        ];

        // 8. Server header — information disclosure
        $server = $getHeader('server');
        $serverExposes = $server && preg_match('/[\d.]+/', $server);
        $checks[] = [
            'name'        => 'Server Header',
            'description' => 'Header Server não deve expor versões de software',
            'status'      => $server ? ($serverExposes ? 'warn' : 'pass') : 'pass',
            'value'       => $server ?: 'Oculto',
            'severity'    => 'low',
            'tip'         => 'Configure o servidor para omitir ou generalizar o header Server.',
        ];

        // 9. X-Powered-By — information disclosure
        $xpb = $getHeader('x-powered-by');
        $checks[] = [
            'name'        => 'X-Powered-By',
            'description' => 'Header X-Powered-By não deve ser exposto',
            'status'      => $xpb ? 'warn' : 'pass',
            'value'       => $xpb ?: 'Oculto',
            'severity'    => 'low',
            'tip'         => 'Remova o header X-Powered-By para não expor tecnologias usadas.',
        ];

        // 10. WordPress specific: xmlrpc.php
        try {
            $xmlrpc = Http::timeout(8)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get(rtrim($baseUrl, '/') . '/xmlrpc.php');
            $xmlrpcExposed = $xmlrpc->status() === 200 || $xmlrpc->status() === 405;
        } catch (\Throwable) {
            $xmlrpcExposed = false;
        }
        $checks[] = [
            'name'        => 'WordPress xmlrpc.php',
            'description' => 'xmlrpc.php exposto pode ser explorado para ataques de força bruta',
            'status'      => $xmlrpcExposed ? 'fail' : 'pass',
            'value'       => $xmlrpcExposed ? 'Exposto — risco de brute force' : 'Bloqueado ou não acessível',
            'severity'    => 'high',
            'tip'         => 'Bloqueie o acesso ao xmlrpc.php via .htaccess ou plugin de segurança.',
        ];

        $this->securityChecks = $checks;

        // Calculate score
        $weights = ['critical' => 30, 'high' => 20, 'medium' => 15, 'low' => 5];
        $total = 0;
        $earned = 0;
        foreach ($checks as $check) {
            $w = $weights[$check['severity']] ?? 5;
            $total += $w;
            if ($check['status'] === 'pass') {
                $earned += $w;
            } elseif ($check['status'] === 'warn') {
                $earned += (int) ($w * 0.5);
            }
        }
        $this->securityScore = $total > 0 ? (int) round(($earned / $total) * 100) : 0;

        $this->securityLoading = false;
    }

    public function detectSchema(): void
    {
        $this->schemaLoading = true;
        $this->schemaError = null;
        $this->schemaItems = [];
        $this->ogTags = [];
        $this->twitterTags = [];

        $baseUrl = $this->baseUrlOrNull();
        if (!$baseUrl) {
            $this->schemaError = 'URL do site inválida.';
            $this->schemaLoading = false;
            return;
        }

        $html = $this->fetchPageHtml($baseUrl);
        if ($html === null) {
            $this->schemaError = 'Não foi possível carregar o HTML da página.';
            $this->schemaLoading = false;
            return;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8" ?>' . $html);
        libxml_clear_errors();

        // Required fields per schema type
        $requiredFields = [
            'Organization'    => ['name', 'url'],
            'WebSite'         => ['name', 'url'],
            'LocalBusiness'   => ['name', 'address', 'telephone'],
            'Article'         => ['headline', 'author', 'datePublished'],
            'BlogPosting'     => ['headline', 'author', 'datePublished'],
            'Product'         => ['name', 'offers'],
            'FAQPage'         => ['mainEntity'],
            'BreadcrumbList'  => ['itemListElement'],
            'Person'          => ['name'],
            'Event'           => ['name', 'startDate', 'location'],
            'Recipe'          => ['name', 'recipeIngredient', 'recipeInstructions'],
            'Review'          => ['itemReviewed', 'reviewRating', 'author'],
            'JobPosting'      => ['title', 'hiringOrganization', 'jobLocation'],
            'VideoObject'     => ['name', 'description', 'thumbnailUrl'],
        ];

        // Extract JSON-LD schemas
        $schemas = [];
        /** @var \DOMElement $script */
        foreach ($dom->getElementsByTagName('script') as $script) {
            if (strtolower($script->getAttribute('type')) !== 'application/ld+json') {
                continue;
            }
            $json = trim($script->textContent);
            if ($json === '') {
                continue;
            }
            $decoded = json_decode($json, true);
            if (!is_array($decoded)) {
                continue;
            }
            // Handle @graph arrays
            if (isset($decoded['@graph']) && is_array($decoded['@graph'])) {
                foreach ($decoded['@graph'] as $item) {
                    if (is_array($item)) {
                        $schemas[] = $item;
                    }
                }
            } else {
                $schemas[] = $decoded;
            }
        }

        foreach ($schemas as $schema) {
            $type = $schema['@type'] ?? null;
            if (!is_string($type)) {
                // handle array of types
                $type = is_array($schema['@type'] ?? null) ? implode(', ', $schema['@type']) : 'Unknown';
            }

            $required = $requiredFields[$type] ?? [];
            $missingFields = [];
            foreach ($required as $field) {
                if (!isset($schema[$field]) || (is_string($schema[$field]) && trim($schema[$field]) === '')) {
                    $missingFields[] = $field;
                }
            }

            $this->schemaItems[] = [
                'type'           => $type,
                'raw'            => json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'missing_fields' => $missingFields,
                'valid'          => empty($missingFields),
                'name'           => $schema['name'] ?? $schema['headline'] ?? null,
            ];
        }

        // Extract Open Graph tags
        $og = [];
        $tw = [];
        /** @var \DOMElement $meta */
        foreach ($dom->getElementsByTagName('meta') as $meta) {
            $property = $meta->getAttribute('property');
            $name     = $meta->getAttribute('name');
            $content  = $meta->getAttribute('content');

            if (str_starts_with($property, 'og:')) {
                $og[substr($property, 3)] = $content;
            }
            if (str_starts_with($name, 'twitter:')) {
                $tw[substr($name, 8)] = $content;
            }
        }
        $this->ogTags = $og;
        $this->twitterTags = $tw;

        $this->schemaLoading = false;
    }

    public function crawlLinks(): void
    {
        @set_time_limit(300);

        $this->linksLoading = true;
        $this->linksError = null;
        $this->links = [];

        $baseUrl = $this->baseUrlOrNull();
        if (!$baseUrl) {
            $this->linksError = 'URL do site inválida.';
            $this->linksLoading = false;
            return;
        }

        // If HTML was not fetched yet, do a quick crawl of the homepage
        if (empty($this->htmlLinks)) {
            $html = $this->fetchPageHtml($baseUrl);
            if ($html !== null) {
                $data = $this->parseHtmlData($html);
                foreach ($data['links'] ?? [] as $href) {
                    $this->htmlLinks[$href] = true;
                }
            }
        }

        // Normalize, deduplicate, resolve relative to base
        $uniqueUrls = [];
        foreach (array_keys($this->htmlLinks) as $href) {
            if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
                $uniqueUrls[$href] = $href;
            } elseif (str_starts_with($href, '/')) {
                $parsed = parse_url($baseUrl);
                $origin = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '');
                $uniqueUrls[$origin . $href] = $origin . $href;
            }
            // Skip protocol-relative, data:, etc.
        }

        // Limit to 150 links to avoid timeouts
        $uniqueUrls = array_slice($uniqueUrls, 0, 150);

        if (empty($uniqueUrls)) {
            $this->linksError = 'Nenhum link encontrado. Execute a auditoria primeiro.';
            $this->linksLoading = false;
            return;
        }

        // Check in batches of 20 using Http::pool()
        $results = [];
        $batches = array_chunk($uniqueUrls, 20, true);

        foreach ($batches as $batch) {
            $batchResults = Http::pool(function ($pool) use ($batch) {
                $out = [];
                foreach ($batch as $key => $url) {
                    $out[$key] = $pool->as($key)->timeout(10)->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (compatible; WebAudit/1.0)',
                    ])->head($url);
                }
                return $out;
            });

            foreach ($batch as $key => $url) {
                try {
                    $response = $batchResults[$key] ?? null;
                    $status = $response ? $response->status() : null;

                    $isInternal = str_starts_with($url, $baseUrl);
                    $type = match(true) {
                        $status === null => 'error',
                        $status >= 200 && $status < 300 => 'ok',
                        $status >= 300 && $status < 400 => 'redirect',
                        $status >= 400 && $status < 500 => 'broken',
                        $status >= 500 => 'error',
                        default => 'unknown',
                    };

                    $results[] = [
                        'url' => $url,
                        'status' => $status,
                        'type' => $type,
                        'internal' => $isInternal,
                    ];
                } catch (\Throwable) {
                    $results[] = [
                        'url' => $url,
                        'status' => null,
                        'type' => 'error',
                        'internal' => str_starts_with($url, $baseUrl),
                    ];
                }
            }
        }

        // Sort: broken first, then error, then redirect, then ok
        $order = ['broken' => 0, 'error' => 1, 'redirect' => 2, 'ok' => 3, 'unknown' => 4];
        usort($results, fn($a, $b) => ($order[$a['type']] ?? 9) <=> ($order[$b['type']] ?? 9));

        $this->links = $results;
        $this->linksLoading = false;
    }

    public function render()
    {
        return view('livewire.clients.web-audit');
    }
}
