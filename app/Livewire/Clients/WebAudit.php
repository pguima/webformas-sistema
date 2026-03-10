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

    public int $totalImages = 0;

    public int $imagesOk = 0;

    public int $imagesError = 0;

    public int $imagesNotWebp = 0;

    public int $imagesWithoutAlt = 0;

    public int $pagesWithoutMetaDescription = 0;

    public int $pagesWithoutMetaKeywords = 0;

    public ?array $pageSpeed = null;

    public array $pageSpeedErrors = [];

    public ?string $pageSpeedStrategy = 'mobile';

    public ?string $pageSpeedLastRunAt = null;

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
        $this->loading = true;
        $this->errorMessage = null;
        $this->general = null;
        $this->images = [];
        $this->pages = [];
        $this->pageSpeed = null;
        $this->pageSpeedErrors = [];

        $this->wpNamespaces = [];

        $this->totalImages = 0;
        $this->imagesOk = 0;
        $this->imagesError = 0;
        $this->imagesNotWebp = 0;
        $this->imagesWithoutAlt = 0;
        $this->pagesWithoutMetaDescription = 0;
        $this->pagesWithoutMetaKeywords = 0;

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

                $out[] = [
                    'id' => $m['id'] ?? null,
                    'url' => $url ?: null,
                    'ext' => $ext ?: null,
                    'width' => $width,
                    'height' => $height,
                    'alt_text' => is_string($alt) ? $alt : null,
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

                if (isset($p['yoast_head_json']) && is_array($p['yoast_head_json'])) {
                    $metaDescription = $p['yoast_head_json']['description'] ?? null;
                    $metaKeywords = $p['yoast_head_json']['keywords'] ?? null;
                }

                if ((!is_string($metaDescription) || trim($metaDescription) === '') && is_int($id)) {
                    $seopress = $this->fetchSeoPressMetaForPost($baseUrl, $id);
                    $metaDescription = $metaDescription ?: $seopress['meta_description'];
                    $metaKeywords = $metaKeywords ?: $seopress['meta_keywords'];
                }

                $out[] = [
                    'id' => $id,
                    'title' => is_string($title) ? strip_tags($title) : null,
                    'slug' => is_string($slug) ? $slug : null,
                    'url' => is_string($link) ? $link : null,
                    'status' => is_string($status) ? $status : null,
                    'meta_description' => is_string($metaDescription) ? $metaDescription : null,
                    'meta_keywords' => is_string($metaKeywords) ? $metaKeywords : null,
                ];
            }

            if (count($items) < 100) {
                break;
            }

            $page++;
        }

        return $out;
    }

    private function computeCounters(): void
    {
        $this->totalImages = count($this->images);

        $ok = 0;
        $err = 0;
        $notWebp = 0;
        $withoutAlt = 0;

        foreach ($this->images as $img) {
            $ext = strtolower((string) ($img['ext'] ?? ''));
            $alt = trim((string) ($img['alt_text'] ?? ''));

            $hasWebp = $ext === 'webp';
            $hasAlt = $alt !== '';

            if (!$hasWebp) {
                $notWebp++;
            }

            if (!$hasAlt) {
                $withoutAlt++;
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

        $pagesNoDesc = 0;
        $pagesNoKeywords = 0;

        foreach ($this->pages as $p) {
            $desc = trim((string) ($p['meta_description'] ?? ''));
            $keywords = trim((string) ($p['meta_keywords'] ?? ''));

            if ($desc === '') {
                $pagesNoDesc++;
            }

            if ($keywords === '') {
                $pagesNoKeywords++;
            }
        }

        $this->pagesWithoutMetaDescription = $pagesNoDesc;
        $this->pagesWithoutMetaKeywords = $pagesNoKeywords;
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
