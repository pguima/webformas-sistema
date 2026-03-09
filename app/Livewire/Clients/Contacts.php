<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Models\Contact;
use Livewire\Component;
use Livewire\WithPagination;

class Contacts extends Component
{
    use WithPagination;

    public Client $client;

    public string $search = '';

    public int $perPage = 10;

    // Form
    public ?int $contactId = null;

    public ?string $name = null;

    public ?string $whatsapp = null;

    public ?string $role = null;

    /** @var array<int, array<string, mixed>> */
    public array $prefetchedContacts = [];

    // Delete
    public ?int $contactToDeleteId = null;

    public string $deleteConfirmation = '';

    public function mount(Client $client): void
    {
        $this->client = $client;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'role' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function create(): void
    {
        $this->reset([
            'contactId',
            'name',
            'whatsapp',
            'role',
        ]);
    }

    public function prefetch(int $id): void
    {
        if (isset($this->prefetchedContacts[$id])) {
            return;
        }

        $contact = Contact::query()
            ->where('client_id', $this->client->id)
            ->select(['id', 'name', 'whatsapp', 'role'])
            ->find($id);

        if (! $contact) {
            return;
        }

        $this->prefetchedContacts[$id] = [
            'name' => $contact->name,
            'whatsapp' => $contact->whatsapp,
            'role' => $contact->role,
        ];
    }

    public function edit(int $id): void
    {
        if (isset($this->prefetchedContacts[$id])) {
            $this->contactId = $id;
            $this->fill($this->prefetchedContacts[$id]);
            return;
        }

        $contact = Contact::query()->where('client_id', $this->client->id)->findOrFail($id);

        $this->contactId = $contact->id;
        $this->name = $contact->name;
        $this->whatsapp = $contact->whatsapp;
        $this->role = $contact->role;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->contactId) {
            $contact = Contact::query()->where('client_id', $this->client->id)->findOrFail($this->contactId);
            $contact->update($data);

            $this->dispatch('notify', message: 'Contato atualizado com sucesso.', variant: 'success', title: 'Sucesso');
        } else {
            Contact::create(array_merge($data, [
                'client_id' => $this->client->id,
            ]));

            $this->dispatch('notify', message: 'Contato criado com sucesso.', variant: 'success', title: 'Sucesso');
        }

        $this->dispatch('close-client-contact-offcanvas');
        $this->create();
    }

    public function confirmDelete(int $id): void
    {
        $this->contactToDeleteId = $id;
        $this->deleteConfirmation = '';
        $this->dispatch('open-delete-modal');
    }

    public function delete(): void
    {
        if ($this->deleteConfirmation !== 'DELETE') {
            $this->addError('deleteConfirmation', 'Digite DELETE para confirmar.');
            return;
        }

        $contact = Contact::query()
            ->where('client_id', $this->client->id)
            ->find($this->contactToDeleteId);

        if (! $contact) {
            $this->dispatch('notify', message: 'Contato não encontrado.', variant: 'danger', title: 'Erro');
            $this->dispatch('close-delete-modal');
            $this->reset(['contactToDeleteId', 'deleteConfirmation']);
            return;
        }

        $contact->delete();

        $this->dispatch('notify', message: 'Contato excluído com sucesso.', variant: 'success', title: 'Sucesso');
        $this->dispatch('close-delete-modal');
        $this->reset(['contactToDeleteId', 'deleteConfirmation']);
    }

    public function render()
    {
        $contacts = Contact::query()
            ->where('client_id', $this->client->id)
            ->when($this->search, function ($query) {
                $q = '%' . $this->search . '%';
                $query->where('name', 'like', $q)
                    ->orWhere('whatsapp', 'like', $q)
                    ->orWhere('role', 'like', $q);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.clients.contacts', [
            'contacts' => $contacts,
            'client' => $this->client,
        ]);
    }
}
