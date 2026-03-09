<?php

namespace App\Livewire\Contacts;

use App\Models\Contact;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

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

    public function render()
    {
        $contacts = Contact::query()
            ->with(['client:id,name,cnpj'])
            ->when($this->search, function ($query) {
                $q = '%' . $this->search . '%';

                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', $q)
                        ->orWhere('whatsapp', 'like', $q)
                        ->orWhere('role', 'like', $q)
                        ->orWhereHas('client', function ($cq) use ($q) {
                            $cq->where('name', 'like', $q)
                                ->orWhere('cnpj', 'like', $q);
                        });
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.contacts.index', [
            'contacts' => $contacts,
        ])->layout('layouts.app');
    }
}
