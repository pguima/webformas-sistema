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

    public string $platform = 'all';

    /** @var array<int, array<string, mixed>> */
    public array $ads = [];

    /** @var array<string, mixed> */
    public array $summary = [];

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

    public function updatedPlatform(): void
    {
        $this->dispatch('campaign-dashboard-data', data: $this->chartData());
    }

    public function loadData(): void
    {
        set_time_limit(120);

        $this->errorMessage = null;
        $this->loading = true;

        if (! $this->hasIds) {
            $this->ads = [];
            $this->summary = [];
            $this->loading = false;
            $this->dispatch('campaign-dashboard-data', data: $this->chartData());
            return;
        }

        try {
            $payload = [
                'managerCustomerId' => $this->campaign->manager_customer_id,
                'clientCustomerId' => $this->campaign->client_customer_id,
                'metaAdsAccountId' => $this->campaign->meta_ads_account_id,
                'periodo' => $this->period,
            ];

            $response = Http::timeout(60)
                ->acceptJson()
                ->asJson()
                ->post('https://webhook.linkspatrocinadosgoogle.com.br/webhook/63f27c63-f077-49ad-aa9f-c7c3a8a6e2d2', $payload);

            $response->throw();

            $data = $response->json();

            $root = null;
            if (is_array($data)) {
                $root = $data[0] ?? null;
            }

            $this->summary = is_array($root) && isset($root['summary']) && is_array($root['summary']) ? $root['summary'] : [];

            $ads = is_array($root) && isset($root['ads']) && is_array($root['ads']) ? $root['ads'] : [];
            $this->ads = array_values(array_filter($ads, fn ($r) => is_array($r)));
        } catch (RequestException $e) {
            $this->ads = [];
            $this->summary = [];
            $this->errorMessage = $e->getMessage();
        } catch (\Throwable $e) {
            $this->ads = [];
            $this->summary = [];
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->loading = false;
            $this->dispatch('campaign-dashboard-data', data: $this->chartData());
        }
    }

    /** @return list<array<string, mixed>> */
    public function getFilteredAdsProperty(): array
    {
        $platform = $this->platform;

        if ($platform === 'google') {
            return array_values(array_filter($this->ads, fn (array $r) => (string) ($r['source'] ?? '') === 'google_ads'));
        }

        if ($platform === 'meta') {
            return array_values(array_filter($this->ads, fn (array $r) => (string) ($r['source'] ?? '') === 'meta_ads'));
        }

        return $this->ads;
    }

    private function chartData(): array
    {
        $rows = $this->filteredAds;

        $sorted = $rows;
        usort($sorted, function (array $a, array $b): int {
            return ((int) ($b['clicks'] ?? 0) <=> (int) ($a['clicks'] ?? 0))
                ?: ((int) ($b['impressions'] ?? 0) <=> (int) ($a['impressions'] ?? 0));
        });

        $top = array_slice($sorted, 0, 8);
        $topCategories    = [];
        $clicks           = [];
        $impressionsDiv100 = [];

        foreach ($top as $r) {
            $name = (string) ($r['campaign_name'] ?? ($r['ad_name'] ?? ($r['campaign_id'] ?? '')));
            $topCategories[]     = Str::limit($name, 22, '…');
            $clicks[]            = (int) ($r['clicks'] ?? 0);
            $impressionsDiv100[] = (int) round(((int) ($r['impressions'] ?? 0)) / 100);
        }

        // --- Status distribution ---
        $statusCounts = ['active' => 0, 'paused' => 0, 'removed' => 0];

        foreach ($rows as $r) {
            $status = strtoupper((string) ($r['campaign_status'] ?? ''));
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
            $aCost = (float) ($a['spend'] ?? 0);
            $bCost = (float) ($b['spend'] ?? 0);

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
            $channel = (string) ($r['channel_type'] ?? ($r['objective'] ?? ''));
            $channel = $channel !== '' ? $channel : '—';
            $channelCounts[$channel] = ($channelCounts[$channel] ?? 0) + 1;
        }

        arsort($channelCounts);

        $costCategories = [];
        $costValues     = [];
        foreach ($topCost as $r) {
            $name = (string) ($r['campaign_name'] ?? ($r['ad_name'] ?? ($r['campaign_id'] ?? '')));
            $costCategories[] = Str::limit($name, 22, '…');
            $costValues[]     = round((float) ($r['spend'] ?? 0), 2);
        }

        $convCategories = [];
        $convValues = [];
        $convRatePct = [];
        foreach ($topConv as $r) {
            $name = (string) ($r['campaign_name'] ?? ($r['ad_name'] ?? ($r['campaign_id'] ?? '')));
            $convCategories[] = Str::limit($name, 22, '…');
            $convValues[] = (float) ($r['conversions'] ?? 0);
            $clicksCount = (int) ($r['clicks'] ?? 0);
            $convRatePct[] = $clicksCount > 0 ? round(((float) ($r['conversions'] ?? 0) / $clicksCount) * 100, 2) : 0.0;
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
        $hasGoogle = (bool) (
            $this->campaign->manager_customer_id
            && trim((string) $this->campaign->manager_customer_id) !== ''
            && $this->campaign->client_customer_id
            && trim((string) $this->campaign->client_customer_id) !== ''
        );

        $hasMeta = (bool) ($this->campaign->meta_ads_account_id && trim((string) $this->campaign->meta_ads_account_id) !== '');

        return $hasGoogle || $hasMeta;
    }

    public function getKpisProperty(): array
    {
        $platform = $this->platform;
        $summary = $this->summary;

        $fromSummary = function (?array $source): array {
            $spend = (float) ($source['spend'] ?? 0);
            $impressions = (int) ($source['impressions'] ?? 0);
            $clicks = (int) ($source['clicks'] ?? 0);
            $ctr = (float) ($source['ctr'] ?? 0);
            $cpm = (float) ($source['cpm'] ?? 0);
            $cpc = array_key_exists('cpc', $source) ? (float) ($source['cpc'] ?? 0) : ($clicks > 0 ? $spend / $clicks : null);
            $conversions = array_key_exists('conversions', $source) ? (float) ($source['conversions'] ?? 0) : null;
            $costPerConversion = array_key_exists('cost_per_conversion', $source) ? (float) ($source['cost_per_conversion'] ?? 0) : null;

            return [
                'spend' => $spend,
                'impressions' => $impressions,
                'clicks' => $clicks,
                'ctr' => $ctr,
                'cpm' => $cpm,
                'cpc' => $cpc,
                'conversions' => $conversions,
                'cost_per_conversion' => $costPerConversion,
            ];
        };

        if ($platform === 'google') {
            $base = $fromSummary(is_array($summary['google_ads'] ?? null) ? $summary['google_ads'] : []);
            $base['extra_label'] = 'Conversões (Google)';
            $base['extra_value'] = (float) data_get($summary, 'google_ads.conversions', 0);
            return $base;
        }

        if ($platform === 'meta') {
            $base = $fromSummary(is_array($summary['meta_ads'] ?? null) ? $summary['meta_ads'] : []);
            $base['extra_label'] = 'Conversas WhatsApp (Meta)';
            $base['extra_value'] = (float) data_get($summary, 'total.meta_whatsapp_conversations', 0);
            return $base;
        }

        $base = $fromSummary(is_array($summary['total'] ?? null) ? $summary['total'] : []);
        $base['extra_label'] = 'Conversões/WhatsApp';
        $base['extra_value'] = (float) ((float) data_get($summary, 'total.google_conversions', 0) + (float) data_get($summary, 'total.meta_whatsapp_conversations', 0));
        return $base;
    }

    public function render()
    {
        return view('livewire.campaigns.dashboard', [
            'chartData' => $this->chartData(),
        ]);
    }
}
