<?php

namespace App\Livewire\Clients;

use App\Livewire\Concerns\HasViewMode;
use App\Models\Client;
use App\Models\Web;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class Webs extends Component
{
    use WithPagination, HasViewMode;

    #[On('client-webs-refresh')]
    public function refreshList(): void
    {
        // no-op: apenas força o componente a re-renderizar
    }

    public Client $client;

    public ?int $auditWebId = null;

    public string $search = '';

    public int $perPage = 10;

    public function mount(Client $client): void
    {
        $this->client = $client;
    }

    public function audit(int $id): void
    {
        $web = Web::query()
            ->where('client_id', $this->client->id)
            ->select(['id'])
            ->find($id);

        if (! $web) {
            return;
        }

        $this->auditWebId = $web->id;
        $this->dispatch('open-client-web-audit-offcanvas');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    // Form
    public ?int $webId = null;

    public ?string $url = null;

    public ?string $type = null;

    public ?string $objective = null;

    public ?string $cta_main = null;

    public ?string $platform = null;

    public ?string $status = null;

    public ?string $responsible = null;

    public ?string $site_created_at = null;

    public ?string $site_updated_at = null;

    public ?string $hosting = null;

    public ?string $domain_until = null;

    public ?string $ssl = null;

    public ?string $certificate_until = null;

    public ?string $gtm_analytics = null;

    public $priority = null;

    public ?string $notes = null;

    /** @var array<int, array<string, mixed>> */
    public array $prefetchedWebs = [];

    // Delete
    public ?int $webToDeleteId = null;

    public string $deleteConfirmation = '';

    public function rules(): array
    {
        return [
            'url' => ['required', 'string', 'max:255'],

            'type' => ['nullable', 'string', 'max:50'],
            'objective' => ['nullable', 'string', 'max:50'],
            'cta_main' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],

            'responsible' => ['nullable', 'string', 'max:255'],

            'site_created_at' => ['nullable', 'date'],
            'site_updated_at' => ['nullable', 'date'],

            'hosting' => ['nullable', 'string', 'max:255'],
            'domain_until' => ['nullable', 'date'],
            'ssl' => ['nullable', 'string', 'max:255'],
            'certificate_until' => ['nullable', 'date'],
            'gtm_analytics' => ['nullable', 'string', 'max:255'],

            'priority' => ['nullable', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function create(): void
    {
        $this->reset([
            'webId',
            'url',

            'type',
            'objective',
            'cta_main',
            'platform',
            'status',
            'responsible',
            'site_created_at',
            'site_updated_at',
            'hosting',
            'domain_until',
            'ssl',
            'certificate_until',
            'gtm_analytics',
            'priority',
            'notes',
        ]);
    }

    public function prefetch(int $id): void
    {
        if (isset($this->prefetchedWebs[$id])) {
            return;
        }

        $web = Web::query()
            ->where('client_id', $this->client->id)
            ->select([
                'id',
                'url',
                'type',
                'objective',
                'cta_main',
                'platform',
                'status',
                'responsible',
                'site_created_at',
                'site_updated_at',
                'hosting',
                'domain_until',
                'ssl',
                'certificate_until',
                'gtm_analytics',
                'priority',
                'notes',
            ])
            ->find($id);

        if (!$web) {
            return;
        }

        $this->prefetchedWebs[$id] = [
            'url' => $web->url,

            'type' => $web->type,
            'objective' => $web->objective,
            'cta_main' => $web->cta_main,
            'platform' => $web->platform,
            'status' => $web->status,
            'responsible' => $web->responsible,
            'site_created_at' => optional($web->site_created_at)->format('Y-m-d'),
            'site_updated_at' => optional($web->site_updated_at)->format('Y-m-d'),
            'hosting' => $web->hosting,
            'domain_until' => optional($web->domain_until)->format('Y-m-d'),
            'ssl' => $web->ssl,
            'certificate_until' => optional($web->certificate_until)->format('Y-m-d'),
            'gtm_analytics' => $web->gtm_analytics,
            'priority' => $web->priority,
            'notes' => $web->notes,
        ];
    }

    public function edit(int $id): void
    {
        if (isset($this->prefetchedWebs[$id])) {
            $this->webId = $id;
            $this->fill($this->prefetchedWebs[$id]);
            return;
        }

        $web = Web::query()->where('client_id', $this->client->id)->findOrFail($id);

        $this->webId = $web->id;
        $this->url = $web->url;

        $this->type = $web->type;
        $this->objective = $web->objective;
        $this->cta_main = $web->cta_main;
        $this->platform = $web->platform;
        $this->status = $web->status;
        $this->responsible = $web->responsible;
        $this->site_created_at = optional($web->site_created_at)->format('Y-m-d');
        $this->site_updated_at = optional($web->site_updated_at)->format('Y-m-d');
        $this->hosting = $web->hosting;
        $this->domain_until = optional($web->domain_until)->format('Y-m-d');
        $this->ssl = $web->ssl;
        $this->certificate_until = optional($web->certificate_until)->format('Y-m-d');
        $this->gtm_analytics = $web->gtm_analytics;
        $this->priority = $web->priority;
        $this->notes = $web->notes;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->webId) {
            $web = Web::query()->where('client_id', $this->client->id)->findOrFail($this->webId);
            $web->update($data);

            $this->dispatch('notify', message: __('app.webs.messages.updated_success'), variant: 'success', title: __('app.webs.messages.success_title'));
        } else {
            Web::create(array_merge($data, [
                'client_id' => $this->client->id,
            ]));

            $this->dispatch('notify', message: __('app.webs.messages.created_success'), variant: 'success', title: __('app.webs.messages.success_title'));
        }

        $this->dispatch('close-client-web-offcanvas');
        $this->create();
    }

    public function confirmDelete(int $id): void
    {
        $this->webToDeleteId = $id;
        $this->deleteConfirmation = '';
        $this->dispatch('open-delete-modal');
    }

    public function delete(): void
    {
        if ($this->deleteConfirmation !== 'DELETE') {
            $this->addError('deleteConfirmation', __('app.webs.delete.placeholder', ['word' => 'DELETE']));
            return;
        }

        $web = Web::query()->where('client_id', $this->client->id)->find($this->webToDeleteId);
        if (!$web) {
            $this->dispatch('notify', message: __('app.webs.messages.error_not_found'), variant: 'danger', title: __('app.webs.messages.error_title'));
            $this->dispatch('close-delete-modal');
            $this->reset(['webToDeleteId', 'deleteConfirmation']);
            return;
        }

        $web->delete();

        $this->dispatch('notify', message: __('app.webs.messages.deleted_success'), variant: 'success', title: __('app.webs.messages.success_title'));
        $this->dispatch('close-delete-modal');
        $this->reset(['webToDeleteId', 'deleteConfirmation']);
    }

    public function render()
    {
        $webs = Web::query()
            ->where('client_id', $this->client->id)
            ->when($this->search, function ($query) {
                $query->where('url', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.clients.webs', [
            'webs' => $webs,
            'client' => $this->client,
        ]);
    }
}
