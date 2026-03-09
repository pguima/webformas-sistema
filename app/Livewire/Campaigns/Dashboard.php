<?php

namespace App\Livewire\Campaigns;

use App\Models\Campaign;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
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

            $response = Http::timeout(30)
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
            $aInteractions = (int) ($a['interactions'] ?? 0);
            $bInteractions = (int) ($b['interactions'] ?? 0);
            $aImpressions = (int) ($a['impressions'] ?? 0);
            $bImpressions = (int) ($b['impressions'] ?? 0);

            return ($bInteractions <=> $aInteractions) ?: ($bImpressions <=> $aImpressions);
        });

        $top = array_slice($sorted, 0, 8);
        $categories = [];
        $clicks = [];
        $impressions = [];

        foreach ($top as $r) {
            $categories[] = (string) ($r['name'] ?? ($r['id'] ?? ''));
            $clicks[] = (int) ($r['interactions'] ?? 0);
            $impressions[] = (int) ($r['impressions'] ?? 0);
        }

        $statusCounts = [
            'active' => 0,
            'paused' => 0,
            'removed' => 0,
        ];

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

        return [
            'top' => [
                'categories' => $categories,
                'clicks' => $clicks,
                'impressions' => $impressions,
            ],
            'status' => [
                'counts' => $statusCounts,
            ],
        ];
    }

    public function getHasIdsProperty(): bool
    {
        return (bool) ($this->campaign->manager_customer_id && $this->campaign->client_customer_id);
    }

    public function getTotalsProperty(): array
    {
        $impressions = 0;
        $interactions = 0;
        $conversions = 0.0;
        $videoViews = 0;
        $costMicros = 0;

        foreach ($this->rows as $r) {
            $impressions += (int) ($r['impressions'] ?? 0);
            $interactions += (int) ($r['interactions'] ?? 0);
            $conversions += (float) ($r['conversions'] ?? 0);
            $videoViews += (int) ($r['videoViews'] ?? 0);
            $costMicros += (int) ($r['costMicros'] ?? 0);
        }

        $cost = $costMicros / 1000000;
        $cpc = $interactions > 0 ? $cost / $interactions : null;
        $cpa = $conversions > 0 ? $cost / $conversions : null;

        return [
            'impressions' => $impressions,
            'interactions' => $interactions,
            'conversions' => $conversions,
            'videoViews' => $videoViews,
            'cost' => $cost,
            'cpc' => $cpc,
            'cpa' => $cpa,
        ];
    }

    public function render()
    {
        return view('livewire.campaigns.dashboard');
    }
}
