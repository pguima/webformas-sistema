<?php

namespace App\Livewire\Clients;

use App\Models\Web;
use App\Models\WebPagespeedHistory;
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

    /** @var array<int, array<string, mixed>> */
    public array $history = [];

    /** @var array<int, array<string, mixed>> Ordered chronologically (oldest first) for charts */
    public array $chartHistory = [];

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

    private function normalizeScore(mixed $value): ?int
    {
        if (!is_numeric($value)) {
            return null;
        }

        $v = (float) $value;
        if ($v <= 1) {
            $v *= 100;
        }

        return max(0, min(100, (int) round($v)));
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

        // Oportunidades: audits com details.type === 'opportunity' e score < 1
        $opportunities = [];
        foreach ($audits as $auditId => $audit) {
            if (($audit['details']['type'] ?? '') !== 'opportunity') {
                continue;
            }

            $auditScore = isset($audit['score']) && is_numeric($audit['score']) ? (float) $audit['score'] : null;
            if ($auditScore === null || $auditScore >= 1.0) {
                continue;
            }

            $savingsMs = $audit['details']['overallSavingsMs'] ?? null;
            $opportunities[] = [
                'id' => $auditId,
                'title' => $audit['title'] ?? $auditId,
                'display_value' => $audit['displayValue'] ?? null,
                'score' => $auditScore,
                'savings_ms' => is_numeric($savingsMs) ? (int) round((float) $savingsMs) : null,
            ];
        }

        usort($opportunities, fn($a, $b) => ($b['savings_ms'] ?? 0) <=> ($a['savings_ms'] ?? 0));

        return [
            'scores' => [
                'performance' => $score('performance'),
                'seo' => $score('seo'),
                'accessibility' => $score('accessibility'),
                'best_practices' => $score('best-practices'),
            ],
            'metrics' => [
                'fcp_ms' => $this->auditNumeric($audits, 'first-contentful-paint'),
                'lcp_ms' => $this->auditNumeric($audits, 'largest-contentful-paint'),
                'tbt_ms' => $this->auditNumeric($audits, 'total-blocking-time'),
                'cls' => $this->auditNumeric($audits, 'cumulative-layout-shift'),
                'ttfb_ms' => $this->auditNumeric($audits, 'server-response-time'),
                'speed_index_ms' => $this->auditNumeric($audits, 'speed-index'),
                'inp_ms' => $this->auditNumeric($audits, 'interaction-to-next-paint'),
            ],
            'display' => [
                'fcp' => $this->auditDisplay($audits, 'first-contentful-paint'),
                'lcp' => $this->auditDisplay($audits, 'largest-contentful-paint'),
                'tbt' => $this->auditDisplay($audits, 'total-blocking-time'),
                'cls' => $this->auditDisplay($audits, 'cumulative-layout-shift'),
                'ttfb' => $this->auditDisplay($audits, 'server-response-time'),
                'speed_index' => $this->auditDisplay($audits, 'speed-index'),
                'inp' => $this->auditDisplay($audits, 'interaction-to-next-paint'),
            ],
            'opportunities' => $opportunities,
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

            $this->history = WebPagespeedHistory::where('web_id', $this->webId)
                ->orderBy('analyzed_at', 'desc')
                ->limit(10)
                ->get()
                ->toArray();

            $this->chartHistory = WebPagespeedHistory::where('web_id', $this->webId)
                ->orderBy('analyzed_at', 'asc')
                ->limit(50)
                ->get(['analyzed_at', 'performance_mobile', 'performance_desktop', 'seo_mobile', 'seo_desktop', 'accessibility_mobile', 'accessibility_desktop', 'best_practices_mobile', 'best_practices_desktop'])
                ->toArray();
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
                try {
                    $this->web?->update([
                        'performance' => $this->normalizeScore($desktopScores['performance'] ?? null),
                        'seo' => $this->normalizeScore($desktopScores['seo'] ?? null),
                        'accessibility' => $this->normalizeScore($desktopScores['accessibility'] ?? null),
                        'best_practices' => $this->normalizeScore($desktopScores['best_practices'] ?? null),
                        'pagespeed_last_checked_at' => now(),
                    ]);

                    $this->web?->refresh();
                    $this->dispatch('client-webs-refresh');
                } catch (\Throwable $e) {
                    $this->dispatch('notify', message: $e->getMessage(), variant: 'danger', title: 'Erro');
                }
            }

            // Salva histórico (não-crítico: falha silenciosa)
            try {
                $mobileScores = $this->pageSpeed['mobile']['scores'] ?? [];
                $desktopScores = $this->pageSpeed['desktop']['scores'] ?? [];

                WebPagespeedHistory::create([
                    'web_id' => $this->webId,
                    'performance_mobile' => $this->normalizeScore($mobileScores['performance'] ?? null),
                    'seo_mobile' => $this->normalizeScore($mobileScores['seo'] ?? null),
                    'accessibility_mobile' => $this->normalizeScore($mobileScores['accessibility'] ?? null),
                    'best_practices_mobile' => $this->normalizeScore($mobileScores['best_practices'] ?? null),
                    'performance_desktop' => $this->normalizeScore($desktopScores['performance'] ?? null),
                    'seo_desktop' => $this->normalizeScore($desktopScores['seo'] ?? null),
                    'accessibility_desktop' => $this->normalizeScore($desktopScores['accessibility'] ?? null),
                    'best_practices_desktop' => $this->normalizeScore($desktopScores['best_practices'] ?? null),
                    'analyzed_at' => now(),
                ]);

                $this->history = WebPagespeedHistory::where('web_id', $this->webId)
                    ->orderBy('analyzed_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->toArray();

                $this->chartHistory = WebPagespeedHistory::where('web_id', $this->webId)
                    ->orderBy('analyzed_at', 'asc')
                    ->limit(50)
                    ->get(['analyzed_at', 'performance_mobile', 'performance_desktop', 'seo_mobile', 'seo_desktop', 'accessibility_mobile', 'accessibility_desktop', 'best_practices_mobile', 'best_practices_desktop'])
                    ->toArray();
            } catch (\Throwable) {
                // histórico não-crítico
            }
        }
    }

    public function render()
    {
        return view('livewire.clients.web-pagespeed');
    }
}
