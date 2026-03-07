<?php

namespace App\Livewire\Plans;

use App\Livewire\Concerns\HasViewMode;
use App\Models\Plan;
use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, HasViewMode;

    public string $search = '';

    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    // Form
    public ?int $planId = null;

    public ?string $name = null;

    public $price = null;

    /** @var array<int, string> */
    public array $service_ids = [];

    public array $prefetchedPlans = [];

    // Delete
    public ?int $planToDeleteId = null;

    public string $deleteConfirmation = '';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'service_ids' => ['array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
        ];
    }

    public function create(): void
    {
        $this->reset(['planId', 'name', 'price', 'service_ids']);
        $this->price = 0;
        $this->service_ids = [];
    }

    public function prefetch(int $id): void
    {
        if (isset($this->prefetchedPlans[$id])) {
            return;
        }

        $plan = Plan::query()
            ->with('services:id')
            ->select(['id', 'name', 'price'])
            ->find($id);

        if (!$plan) {
            return;
        }

        $this->prefetchedPlans[$id] = [
            'name' => $plan->name,
            'price' => (string) $plan->price,
            'service_ids' => $plan->services->pluck('id')->map(fn ($v) => (string) $v)->values()->all(),
        ];
    }

    public function edit(int $id): void
    {
        if (isset($this->prefetchedPlans[$id])) {
            $this->planId = $id;
            $this->name = $this->prefetchedPlans[$id]['name'] ?? null;
            $this->price = $this->prefetchedPlans[$id]['price'] ?? 0;
            $this->service_ids = $this->prefetchedPlans[$id]['service_ids'] ?? [];
            return;
        }

        $plan = Plan::query()->with('services:id')->findOrFail($id);

        $this->planId = $plan->id;
        $this->name = $plan->name;
        $this->price = $plan->price;
        $this->service_ids = $plan->services->pluck('id')->map(fn ($v) => (string) $v)->values()->all();
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->planId) {
            $plan = Plan::findOrFail($this->planId);
            $plan->update([
                'name' => $data['name'],
                'price' => $data['price'],
            ]);

            $plan->services()->sync($data['service_ids'] ?? []);

            $this->dispatch('notify', message: __('app.plans.messages.updated_success'), variant: 'success', title: __('app.plans.messages.success_title'));
        } else {
            $plan = Plan::create([
                'name' => $data['name'],
                'price' => $data['price'],
            ]);

            $plan->services()->sync($data['service_ids'] ?? []);

            $this->dispatch('notify', message: __('app.plans.messages.created_success'), variant: 'success', title: __('app.plans.messages.success_title'));
        }

        $this->dispatch('close-plan-offcanvas');
        $this->reset(['planId', 'name', 'price', 'service_ids']);
        $this->price = null;
        $this->service_ids = [];
    }

    public function confirmDelete(int $id): void
    {
        $this->planToDeleteId = $id;
        $this->deleteConfirmation = '';
        $this->dispatch('open-delete-modal');
    }

    public function delete(): void
    {
        if ($this->deleteConfirmation !== 'DELETE') {
            $this->addError('deleteConfirmation', __('app.plans.delete.placeholder', ['word' => 'DELETE']));
            return;
        }

        $plan = Plan::find($this->planToDeleteId);
        if (!$plan) {
            $this->dispatch('notify', message: __('app.plans.messages.error_not_found'), variant: 'danger', title: __('app.plans.messages.error_title'));
            $this->dispatch('close-delete-modal');
            $this->reset(['planToDeleteId', 'deleteConfirmation']);
            return;
        }

        $plan->delete();

        $this->dispatch('notify', message: __('app.plans.messages.deleted_success'), variant: 'success', title: __('app.plans.messages.success_title'));
        $this->dispatch('close-delete-modal');
        $this->reset(['planToDeleteId', 'deleteConfirmation']);
    }

    public function render()
    {
        $plans = Plan::query()
            ->withCount('services')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        $services = Service::query()->orderBy('name')->get(['id', 'name']);

        $serviceOptions = $services->map(fn ($s) => ['value' => (string) $s->id, 'label' => $s->name])->values()->all();

        return view('livewire.plans.index', [
            'plans' => $plans,
            'serviceOptions' => $serviceOptions,
        ])->layout('layouts.app');
    }
}
