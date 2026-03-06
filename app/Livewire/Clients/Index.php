<?php

namespace App\Livewire\Clients;

use App\Livewire\Concerns\HasViewMode;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, HasViewMode;

    // Search
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
    public ?int $clientId = null;

    public ?string $name = null;

    public ?string $cnpj = null;

    public ?string $category = null;

    public array $prefetchedClients = [];

    // Delete
    public ?int $clientToDeleteId = null;

    public string $deleteConfirmation = '';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'cnpj' => ['nullable', 'string', 'max:18'],
            'category' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function create(): void
    {
        $this->reset(['clientId', 'name', 'cnpj', 'category']);
    }

    public function prefetch(int $id): void
    {
        if (isset($this->prefetchedClients[$id])) {
            return;
        }

        $client = Client::query()
            ->select(['id', 'name', 'cnpj', 'category'])
            ->find($id);

        if (!$client) {
            return;
        }

        $this->prefetchedClients[$id] = [
            'name' => $client->name,
            'cnpj' => $client->cnpj,
            'category' => $client->category,
        ];
    }

    public function edit(int $id): void
    {
        if (isset($this->prefetchedClients[$id])) {
            $this->clientId = $id;
            $this->name = $this->prefetchedClients[$id]['name'] ?? null;
            $this->cnpj = $this->prefetchedClients[$id]['cnpj'] ?? null;
            $this->category = $this->prefetchedClients[$id]['category'] ?? null;
            return;
        }

        $client = Client::findOrFail($id);
        $this->clientId = $client->id;
        $this->name = $client->name;
        $this->cnpj = $client->cnpj;
        $this->category = $client->category;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->clientId) {
            $client = Client::findOrFail($this->clientId);
            $client->update([
                'name' => $this->name,
                'cnpj' => $this->cnpj,
                'category' => $this->category,
            ]);

            $this->dispatch('notify', message: __('app.clients.messages.updated_success'), variant: 'success', title: __('app.clients.messages.success_title'));
        } else {
            Client::create([
                'name' => $this->name,
                'cnpj' => $this->cnpj,
                'category' => $this->category,
            ]);

            $this->dispatch('notify', message: __('app.clients.messages.created_success'), variant: 'success', title: __('app.clients.messages.success_title'));
        }

        $this->dispatch('close-client-offcanvas');
        $this->reset(['clientId', 'name', 'cnpj', 'category']);
    }

    public function confirmDelete(int $id): void
    {
        $this->clientToDeleteId = $id;
        $this->deleteConfirmation = '';
        $this->dispatch('open-delete-modal');
    }

    public function delete(): void
    {
        if ($this->deleteConfirmation !== 'DELETE') {
            $this->addError('deleteConfirmation', __('app.clients.delete.placeholder', ['word' => 'DELETE']));
            return;
        }

        $client = Client::find($this->clientToDeleteId);

        if (!$client) {
            $this->dispatch('notify', message: __('app.clients.messages.error_not_found'), variant: 'danger', title: __('app.clients.messages.error_title'));
            $this->dispatch('close-delete-modal');
            $this->reset(['clientToDeleteId', 'deleteConfirmation']);
            return;
        }

        $client->delete();

        $this->dispatch('notify', message: __('app.clients.messages.deleted_success'), variant: 'success', title: __('app.clients.messages.success_title'));
        $this->dispatch('close-delete-modal');
        $this->reset(['clientToDeleteId', 'deleteConfirmation']);
    }

    public function render()
    {
        $clients = Client::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('cnpj', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.clients.index', [
            'clients' => $clients,
        ])->layout('layouts.app');
    }
}
