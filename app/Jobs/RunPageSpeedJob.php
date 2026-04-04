<?php

namespace App\Jobs;

use App\Models\Web;
use App\Models\WebPagespeedHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RunPageSpeedJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 180;

    public function __construct(public readonly int $webId)
    {
    }

    public function handle(): void
    {
        $web = Web::find($this->webId);
        if (!$web || !$web->url) {
            return;
        }

        $url = $this->normalizeUrl($web->url);
        if (!$url) {
            return;
        }

        $key = config('services.pagespeed.key');
        $strategies = ['mobile', 'desktop'];
        $urls = [];
        foreach ($strategies as $strategy) {
            $urls[$strategy] = $this->buildApiUrl($url, $strategy, $key);
        }

        $responses = Http::pool(function ($pool) use ($urls) {
            $out = [];
            foreach ($urls as $strategy => $apiUrl) {
                $out[$strategy] = $pool->as($strategy)->timeout(120)->acceptJson()->get($apiUrl);
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
                if (is_array($json)) {
                    $out[$strategy] = $this->parseScores($json);
                }
            } catch (\Throwable) {
                // silent — non-critical scheduled job
            }
        }

        if (empty($out)) {
            return;
        }

        $desktop = $out['desktop'] ?? null;
        $mobile = $out['mobile'] ?? null;

        if ($desktop) {
            $web->update([
                'performance'              => $desktop['performance'],
                'seo'                      => $desktop['seo'],
                'accessibility'            => $desktop['accessibility'],
                'best_practices'           => $desktop['best_practices'],
                'pagespeed_last_checked_at' => now(),
            ]);
        }

        WebPagespeedHistory::create([
            'web_id'                  => $web->id,
            'performance_mobile'      => $mobile['performance'] ?? null,
            'seo_mobile'              => $mobile['seo'] ?? null,
            'accessibility_mobile'    => $mobile['accessibility'] ?? null,
            'best_practices_mobile'   => $mobile['best_practices'] ?? null,
            'performance_desktop'     => $desktop['performance'] ?? null,
            'seo_desktop'             => $desktop['seo'] ?? null,
            'accessibility_desktop'   => $desktop['accessibility'] ?? null,
            'best_practices_desktop'  => $desktop['best_practices'] ?? null,
            'analyzed_at'             => now(),
        ]);
    }

    private function normalizeUrl(string $url): ?string
    {
        $trimmed = trim($url);
        if ($trimmed === '') {
            return null;
        }
        if (!Str::startsWith($trimmed, ['http://', 'https://'])) {
            $trimmed = 'https://' . $trimmed;
        }
        return rtrim($trimmed, '/');
    }

    private function buildApiUrl(string $url, string $strategy, ?string $key): string
    {
        $categories = ['performance', 'seo', 'accessibility', 'best-practices'];
        $query = ['url' => $url, 'strategy' => $strategy];
        if ($key) {
            $query['key'] = $key;
        }
        $qs = http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        foreach ($categories as $cat) {
            $qs .= '&category=' . rawurlencode($cat);
        }
        return 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?' . $qs;
    }

    private function parseScores(array $json): array
    {
        $cats = $json['lighthouseResult']['categories'] ?? [];
        $normalize = function ($v): ?int {
            if (!is_numeric($v)) {
                return null;
            }
            $v = (float) $v;
            if ($v <= 1) {
                $v *= 100;
            }
            return max(0, min(100, (int) round($v)));
        };

        return [
            'performance'   => $normalize($cats['performance']['score'] ?? null),
            'seo'           => $normalize($cats['seo']['score'] ?? null),
            'accessibility' => $normalize($cats['accessibility']['score'] ?? null),
            'best_practices'=> $normalize($cats['best-practices']['score'] ?? null),
        ];
    }
}
