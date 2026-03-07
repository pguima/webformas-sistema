<?php

namespace App\Livewire\Services;

use App\Livewire\Concerns\HasViewMode;
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
    public ?int $serviceId = null;

    public ?string $name = null;

    public $price = null;

    public array $prefetchedServices = [];

    // Delete
    public ?int $serviceToDeleteId = null;

    public string $deleteConfirmation = '';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function create(): void
    {
        $this->reset(['serviceId', 'name', 'price']);
        $this->price = 0;
    }

    public function prefetch(int $id): void
    {
        if (isset($this->prefetchedServices[$id])) {
            return;
        }

        $service = Service::query()
            ->select(['id', 'name', 'price'])
            ->find($id);

        if (!$service) {
            return;
        }

        $this->prefetchedServices[$id] = [
            'name' => $service->name,
            'price' => (string) $service->price,
        ];
    }

    public function edit(int $id): void
    {
        if (isset($this->prefetchedServices[$id])) {
            $this->serviceId = $id;
            $this->name = $this->prefetchedServices[$id]['name'] ?? null;
            $this->price = $this->prefetchedServices[$id]['price'] ?? 0;
            return;
        }

        $service = Service::findOrFail($id);

        $this->serviceId = $service->id;
        $this->name = $service->name;
        $this->price = $service->price;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->serviceId) {
            $service = Service::findOrFail($this->serviceId);
            $service->update($data);

            $this->dispatch('notify', message: __('app.services.messages.updated_success'), variant: 'success', title: __('app.services.messages.success_title'));
        } else {
            Service::create($data);

            $this->dispatch('notify', message: __('app.services.messages.created_success'), variant: 'success', title: __('app.services.messages.success_title'));
        }

        $this->dispatch('close-service-offcanvas');
        $this->reset(['serviceId', 'name', 'price']);
        $this->price = null;
    }

    public function confirmDelete(int $id): void
    {
        $this->serviceToDeleteId = $id;
        $this->deleteConfirmation = '';
        $this->dispatch('open-delete-modal');
    }

    public function delete(): void
    {
        if ($this->deleteConfirmation !== 'DELETE') {
            $this->addError('deleteConfirmation', __('app.services.delete.placeholder', ['word' => 'DELETE']));
            return;
        }

        $service = Service::find($this->serviceToDeleteId);
        if (!$service) {
            $this->dispatch('notify', message: __('app.services.messages.error_not_found'), variant: 'danger', title: __('app.services.messages.error_title'));
            $this->dispatch('close-delete-modal');
            $this->reset(['serviceToDeleteId', 'deleteConfirmation']);
            return;
        }

        $service->delete();

        $this->dispatch('notify', message: __('app.services.messages.deleted_success'), variant: 'success', title: __('app.services.messages.success_title'));
        $this->dispatch('close-delete-modal');
        $this->reset(['serviceToDeleteId', 'deleteConfirmation']);
    }

    public function render()
    {
        $services = Service::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.services.index', [
            'services' => $services,
        ])->layout('layouts.app');
    }
}
