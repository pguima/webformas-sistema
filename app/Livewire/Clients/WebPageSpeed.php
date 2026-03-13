<?php

namespace App\Livewire\Clients;

use App\Models\Web;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class WebPageSpeed extends Component
{
    public int $webId;

    public ?Web $web = null;

    public bool $loading = false;

    public ?string $errorMessage = null;

    public ?array $pageSpeed = null;

    public array $pageSpeedErrors = [];

    public ?string $pageSpeedLastRunAt = null;

    public function mount(int $webId): void
    {
        $this->webId = $webId;
        $this->loadData();
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

    private function auditNumeric(array $audits, string $key, string $field = 'numericValue'): ?float
    {
        $v = $audits[$key][$field] ?? null;
        return is_numeric($v) ? (float) $v : null;
    }

    private function auditDisplay(array $audits, string $key): ?string
    {
        $v = $audits[$key]['displayValue'] ?? null;
        return is_string($v) && trim($v) !== '' ? $v : null;
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

        $fcpMs = $this->auditNumeric($audits, 'first-contentful-paint');
        $lcpMs = $this->auditNumeric($audits, 'largest-contentful-paint');
        $tbtMs = $this->auditNumeric($audits, 'total-blocking-time');
        $cls = $this->auditNumeric($audits, 'cumulative-layout-shift');
        $speedIndexMs = $this->auditNumeric($audits, 'speed-index');
        $ttfbMs = $this->auditNumeric($audits, 'server-response-time');

        return [
            'scores' => [
                'performance' => $score('performance'),
                'seo' => $score('seo'),
                'accessibility' => $score('accessibility'),
                'best_practices' => $score('best-practices'),
            ],
            'metrics' => [
                'fcp_ms' => $fcpMs,
                'lcp_ms' => $lcpMs,
                'tbt_ms' => $tbtMs,
                'cls' => $cls,
                'ttfb_ms' => $ttfbMs,
                'speed_index_ms' => $speedIndexMs,
            ],
            'display' => [
                'fcp' => $this->auditDisplay($audits, 'first-contentful-paint'),
                'lcp' => $this->auditDisplay($audits, 'largest-contentful-paint'),
                'tbt' => $this->auditDisplay($audits, 'total-blocking-time'),
                'cls' => $this->auditDisplay($audits, 'cumulative-layout-shift'),
                'ttfb' => $this->auditDisplay($audits, 'server-response-time'),
                'speed_index' => $this->auditDisplay($audits, 'speed-index'),
            ],
        ];
    }

    public function loadData(): void
    {
        $this->loading = true;
        $this->errorMessage = null;
        $this->pageSpeed = null;
        $this->pageSpeedErrors = [];

        try {
            $this->web = Web::query()->with(['client:id,name'])->findOrFail($this->webId);

            $baseUrl = $this->normalizeBaseUrl($this->web->url);
            if (!$baseUrl) {
                $this->errorMessage = 'URL do site inválida.';
                return;
            }

            if ($this->web->pagespeed_last_checked_at) {
                $this->pageSpeedLastRunAt = $this->web->pagespeed_last_checked_at->format('d/m/Y H:i');
            }
        } catch (RequestException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function analyzePageSpeed(): void
    {
        @set_time_limit(180);

        if (!$this->web) {
            $this->loadData();
        }

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

                    $this->web?->refresh();

                    $this->dispatch('client-webs-refresh');
                } catch (\Throwable $e) {
                    $this->dispatch('notify', message: $e->getMessage(), variant: 'danger', title: 'Erro');
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.clients.web-pagespeed');
    }
}
