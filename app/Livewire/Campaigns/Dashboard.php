<?php

namespace App\Livewire\Campaigns;

use App\Models\Campaign;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class Dashboard extends Component
{
    public Campaign $campaign;

    public string $period = 'All Time';

    /** @var array<int, array<string, mixed>> */
    public array $rows = [];

    public bool $loading = false;

    public ?string $errorMessage = null;

    public function mount(Campaign $campaign): void
    {
        $this->campaign = $campaign;
    }

    public function updatedPeriod(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        set_time_limit(120);

        $this->errorMessage = null;
        $this->loading = true;

        if (! $this->hasIds) {
            $this->rows = [];
            $this->loading = false;
            $this->dispatch('campaign-dashboard-data', data: $this->chartData());
            return;
        }

        try {
            $payload = [
                'managerCustomerId' => $this->campaign->manager_customer_id,
                'clientCustomerId' => $this->campaign->client_customer_id,
                'periodo' => $this->period,
            ];

            $response = Http::timeout(60)
                ->acceptJson()
                ->asJson()
                ->post('https://webhook.linkspatrocinadosgoogle.com.br/webhook/63f27c63-f077-49ad-aa9f-c7c3a8a6e2d2', $payload);

            $response->throw();

            $data = $response->json();
            $this->rows = is_array($data) ? $data : [];
        } catch (RequestException $e) {
            $this->rows = [];
            $this->errorMessage = $e->getMessage();
        } catch (\Throwable $e) {
            $this->rows = [];
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->loading = false;
            $this->dispatch('campaign-dashboard-data', data: $this->chartData());
        }
    }

    private function chartData(): array
    {
        $rows = $this->rows;

        $sorted = $rows;
        usort($sorted, function (array $a, array $b): int {
            return ((int) ($b['interactions'] ?? 0) <=> (int) ($a['interactions'] ?? 0))
                ?: ((int) ($b['impressions'] ?? 0) <=> (int) ($a['impressions'] ?? 0));
        });

        $top = array_slice($sorted, 0, 8);
        $topCategories    = [];
        $clicks           = [];
        $impressionsDiv100 = [];

        foreach ($top as $r) {
            $topCategories[]     = Str::limit((string) ($r['name'] ?? ($r['id'] ?? '')), 22, '…');
            $clicks[]            = (int) ($r['interactions'] ?? 0);
            $impressionsDiv100[] = (int) round(((int) ($r['impressions'] ?? 0)) / 100);
        }

        // --- Status distribution ---
        $statusCounts = ['active' => 0, 'paused' => 0, 'removed' => 0];

        foreach ($rows as $r) {
            $status = strtoupper((string) ($r['status'] ?? ''));
            if (in_array($status, ['ENABLED', 'ACTIVE', 'ATIVA', 'ATIVO'], true)) {
                $statusCounts['active']++;
            } elseif (in_array($status, ['PAUSED', 'PAUSADA', 'PAUSADO'], true)) {
                $statusCounts['paused']++;
            } elseif (in_array($status, ['REMOVED', 'REMOVIDA', 'REMOVIDO'], true)) {
                $statusCounts['removed']++;
            }
        }

        $sortedByCost = $rows;
        usort($sortedByCost, function (array $a, array $b): int {
            $aCost = (int) ($a['costMicros'] ?? 0);
            $bCost = (int) ($b['costMicros'] ?? 0);

            return $bCost <=> $aCost;
        });

        $sortedByConversions = $rows;
        usort($sortedByConversions, function (array $a, array $b): int {
            $aConv = (float) ($a['conversions'] ?? 0);
            $bConv = (float) ($b['conversions'] ?? 0);

            return $bConv <=> $aConv;
        });

        $topCost = array_slice($sortedByCost, 0, 8);
        $topConv = array_slice($sortedByConversions, 0, 8);
        $channelCounts = [];
        foreach ($rows as $r) {
            $channel = (string) ($r['advertisingChannelType'] ?? '');
            $channel = $channel !== '' ? $channel : '—';
            $channelCounts[$channel] = ($channelCounts[$channel] ?? 0) + 1;
        }

        arsort($channelCounts);

        $costCategories = [];
        $costValues     = [];
        foreach ($topCost as $r) {
            $costCategories[] = Str::limit((string) ($r['name'] ?? ($r['id'] ?? '')), 22, '…');
            $costValues[]     = round(((int) ($r['costMicros'] ?? 0)) / 1_000_000, 2);
        }

        $convCategories = [];
        $convValues = [];
        $convRatePct = [];
        foreach ($topConv as $r) {
            $convCategories[] = Str::limit((string) ($r['name'] ?? ($r['id'] ?? '')), 22, '…');
            $convValues[] = (float) ($r['conversions'] ?? 0);
            $rate = (float) ($r['conversionsFromInteractionsRate'] ?? 0);
            $convRatePct[] = round($rate * 100, 2);
        }

        return [
            'top' => [
                'categories'      => $topCategories,
                'clicks'          => $clicks,
                'impressions_100' => $impressionsDiv100,
            ],
            'status' => [
                'counts' => $statusCounts,
            ],
            'channel' => [
                'labels' => array_keys($channelCounts),
                'counts' => array_values($channelCounts),
            ],
            'topCost' => [
                'categories' => $costCategories,
                'values'     => $costValues,
            ],
            'conv' => [
                'categories' => $convCategories,
                'values'     => $convValues,
                'rate_pct'   => $convRatePct,
            ],
        ];
    }

    public function getHasIdsProperty(): bool
    {
        return (bool) ($this->campaign->manager_customer_id && $this->campaign->client_customer_id);
    }

    public function getTotalsProperty(): array
    {
        $impressions   = 0;
        $interactions  = 0;
        $conversions   = 0.0;
        $videoViews    = 0;
        $costMicros    = 0;
        $totalBudget   = 0;
        $optScoreSum   = 0.0;
        $optScoreCount = 0;

        foreach ($this->rows as $r) {
            $impressions  += (int) ($r['impressions'] ?? 0);
            $interactions += (int) ($r['interactions'] ?? 0);
            $conversions  += (float) ($r['conversions'] ?? 0);
            $videoViews   += (int) ($r['videoViews'] ?? 0);
            $costMicros   += (int) ($r['costMicros'] ?? 0);
            $totalBudget  += (int) ($r['amountMicros'] ?? 0);

            if (array_key_exists('optimizationScore', $r) && $r['optimizationScore'] !== null) {
                $optScoreSum += (float) $r['optimizationScore'];
                $optScoreCount++;
            }
        }

        $cost              = $costMicros / 1_000_000;
        $cpc               = $interactions > 0 ? $cost / $interactions : null;
        $cpa               = $conversions > 0 ? $cost / $conversions : null;
        $ctr               = $impressions > 0 ? ($interactions / $impressions) * 100 : 0.0;
        $avgCpm            = $impressions > 0 ? ($cost / $impressions) * 1000 : null;
        $optimizationScore = $optScoreCount > 0 ? ($optScoreSum / $optScoreCount) * 100 : null;
        $totalBudgetResult = $totalBudget / 1_000_000;

        return [
            'impressions'       => $impressions,
            'interactions'      => $interactions,
            'conversions'       => $conversions,
            'videoViews'        => $videoViews,
            'cost'              => $cost,
            'cpc'               => $cpc,
            'cpa'               => $cpa,
            'ctr'               => $ctr,
            'avgCpm'            => $avgCpm,
            'optimizationScore' => $optimizationScore,
            'totalBudget'       => $totalBudgetResult,
        ];
    }

    public function render()
    {
        return view('livewire.campaigns.dashboard', [
            'chartData' => $this->chartData(),
        ]);
    }
}
