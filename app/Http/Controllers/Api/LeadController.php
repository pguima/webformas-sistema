<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Livewire\Leads\Index as LeadsIndex;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
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
            'plan' => ['nullable', 'string', 'max:255'],
            'services' => ['nullable', 'string'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'responsible_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'origin' => ['nullable', 'string', 'max:255'],
            'campaign' => ['nullable', 'string', 'max:255'],
            'stage' => ['nullable', 'string', Rule::in(LeadsIndex::STAGES)],
            'external_id' => ['nullable', 'string', 'max:255'],
            'payload' => ['nullable', 'array'],
        ]);

        $stage = $data['stage'] ?? 'Novo';
        $position = (int) (Lead::query()->where('stage', $stage)->max('position') ?? 0);

        $lead = Lead::create([
            'name' => $data['name'],
            'whatsapp' => $data['whatsapp'] ?? null,
            'plan' => $data['plan'] ?? null,
            'services' => $data['services'] ?? null,
            'value' => $data['value'] ?? null,
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
            'plan' => ['nullable', 'string', 'max:255'],
            'services' => ['nullable', 'string'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'responsible_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'origin' => ['nullable', 'string', 'max:255'],
            'campaign' => ['nullable', 'string', 'max:255'],
            'stage' => ['nullable', 'string', Rule::in(LeadsIndex::STAGES)],
            'position' => ['nullable', 'integer', 'min:0'],
            'external_id' => ['nullable', 'string', 'max:255'],
            'payload' => ['nullable', 'array'],
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
