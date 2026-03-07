<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Livewire\Leads\Index as LeadsIndex;
use App\Models\Lead;
use App\Models\Plan;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    private function computePricing(?int $planId, array $serviceIds, ?string $discountType, float $discountValue): array
    {
        $base = 0.0;

        if (!empty($planId)) {
            $base = (float) (Plan::query()->whereKey($planId)->value('price') ?? 0);
            $serviceIds = Plan::query()
                ->find($planId)
                ?->services()
                ->pluck('services.id')
                ->all() ?? [];
        } else {
            $ids = array_values(array_unique(array_filter(array_map('intval', $serviceIds))));
            if (!empty($ids)) {
                $base = (float) (Service::query()->whereIn('id', $ids)->sum('price') ?? 0);
            }
        }

        $discountAmount = 0.0;
        if ($discountType === 'percent') {
            $discountAmount = $base * ($discountValue / 100.0);
        } elseif ($discountType === 'value') {
            $discountAmount = $discountValue;
        }

        $final = max(0.0, $base - $discountAmount);

        return [
            'service_ids' => array_values(array_map('intval', $serviceIds)),
            'value_base' => round($base, 2),
            'value_final' => round($final, 2),
        ];
    }

    public function index(Request $request)
    {
        $leads = Lead::query()
            ->latest()
            ->get();

        return response()->json(['data' => $leads]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'discount_type' => ['nullable', Rule::in(['value', 'percent'])],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'responsible_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'origin' => ['nullable', 'string', 'max:255'],
            'campaign' => ['nullable', 'string', 'max:255'],
            'stage' => ['nullable', 'string', Rule::in(LeadsIndex::STAGES)],
            'external_id' => ['nullable', 'string', 'max:255'],
            'payload' => ['nullable', 'array'],
        ]);

        $discountType = $data['discount_type'] ?? 'value';
        $discountValue = isset($data['discount_value']) ? (float) $data['discount_value'] : 0.0;
        $computed = $this->computePricing(
            isset($data['plan_id']) ? (int) $data['plan_id'] : null,
            $data['service_ids'] ?? [],
            $discountType,
            $discountValue
        );

        $planName = null;
        if (!empty($data['plan_id'])) {
            $planName = Plan::query()->whereKey($data['plan_id'])->value('name');
        }

        $serviceNames = [];
        if (!empty($computed['service_ids'])) {
            $serviceNames = Service::query()->whereIn('id', $computed['service_ids'])->orderBy('name')->pluck('name')->all();
        }

        $stage = $data['stage'] ?? 'Novo';
        $position = (int) (Lead::query()->where('stage', $stage)->max('position') ?? 0);

        $lead = Lead::create([
            'name' => $data['name'],
            'whatsapp' => $data['whatsapp'] ?? null,
            'plan_id' => $data['plan_id'] ?? null,
            'plan' => $planName,
            'service_ids' => $computed['service_ids'] ?? null,
            'services' => !empty($serviceNames) ? implode(', ', $serviceNames) : null,
            'value_base' => $computed['value_base'] ?? null,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'value_final' => $computed['value_final'] ?? null,
            'value' => $computed['value_final'] ?? null,
            'responsible_user_id' => $data['responsible_user_id'] ?? null,
            'origin' => $data['origin'] ?? null,
            'campaign' => $data['campaign'] ?? null,
            'stage' => $stage,
            'position' => $position + 1,
            'external_id' => $data['external_id'] ?? null,
            'payload' => $data['payload'] ?? null,
        ]);

        return response()->json(['data' => $lead], 201);
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'discount_type' => ['nullable', Rule::in(['value', 'percent'])],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'responsible_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'origin' => ['nullable', 'string', 'max:255'],
            'campaign' => ['nullable', 'string', 'max:255'],
            'stage' => ['nullable', 'string', Rule::in(LeadsIndex::STAGES)],
            'position' => ['nullable', 'integer', 'min:0'],
            'external_id' => ['nullable', 'string', 'max:255'],
            'payload' => ['nullable', 'array'],
        ]);

        $planId = array_key_exists('plan_id', $data) ? ($data['plan_id'] ? (int) $data['plan_id'] : null) : $lead->plan_id;
        $serviceIds = array_key_exists('service_ids', $data) ? ($data['service_ids'] ?? []) : ($lead->service_ids ?? []);
        $discountType = array_key_exists('discount_type', $data) ? ($data['discount_type'] ?? null) : $lead->discount_type;
        $discountValue = array_key_exists('discount_value', $data) ? (float) ($data['discount_value'] ?? 0) : (float) ($lead->discount_value ?? 0);

        $computed = $this->computePricing($planId, $serviceIds, $discountType, $discountValue);

        $planName = null;
        if (!empty($planId)) {
            $planName = Plan::query()->whereKey($planId)->value('name');
        }

        $serviceNames = [];
        if (!empty($computed['service_ids'])) {
            $serviceNames = Service::query()->whereIn('id', $computed['service_ids'])->orderBy('name')->pluck('name')->all();
        }

        $data = array_merge($data, [
            'plan_id' => $planId,
            'plan' => $planName,
            'service_ids' => $computed['service_ids'] ?? null,
            'services' => !empty($serviceNames) ? implode(', ', $serviceNames) : null,
            'value_base' => $computed['value_base'] ?? null,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'value_final' => $computed['value_final'] ?? null,
            'value' => $computed['value_final'] ?? null,
        ]);

        $lead->update($data);

        return response()->json(['data' => $lead->fresh()]);
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return response()->json(['ok' => true]);
    }
}
