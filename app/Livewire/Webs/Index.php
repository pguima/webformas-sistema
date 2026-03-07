<?php

namespace App\Livewire\Webs;

use App\Livewire\Concerns\HasViewMode;
use App\Models\Client;
use App\Models\Web;
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

    // Form (no create on this page)
    public ?int $webId = null;

    public ?int $client_id = null;

    public ?string $name = null;

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

    public $pagespeed_mobile = null;

    public $pagespeed_desktop = null;

    public $seo_score = null;

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
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],

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

            'pagespeed_mobile' => ['nullable', 'integer', 'min:0', 'max:100'],
            'pagespeed_desktop' => ['nullable', 'integer', 'min:0', 'max:100'],
            'seo_score' => ['nullable', 'integer', 'min:0', 'max:100'],

            'priority' => ['nullable', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function prefetch(int $id): void
    {
        if (isset($this->prefetchedWebs[$id])) {
            return;
        }

        $web = Web::query()
            ->select([
                'id',
                'client_id',
                'name',
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
                'pagespeed_mobile',
                'pagespeed_desktop',
                'seo_score',
                'priority',
                'notes',
            ])
            ->find($id);

        if (!$web) {
            return;
        }

        $this->prefetchedWebs[$id] = [
            'client_id' => $web->client_id,
            'name' => $web->name,
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
            'pagespeed_mobile' => $web->pagespeed_mobile,
            'pagespeed_desktop' => $web->pagespeed_desktop,
            'seo_score' => $web->seo_score,
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

        $web = Web::findOrFail($id);

        $this->webId = $web->id;
        $this->client_id = $web->client_id;
        $this->name = $web->name;
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
        $this->pagespeed_mobile = $web->pagespeed_mobile;
        $this->pagespeed_desktop = $web->pagespeed_desktop;
        $this->seo_score = $web->seo_score;
        $this->priority = $web->priority;
        $this->notes = $web->notes;
    }

    public function save(): void
    {
        if (!$this->webId) {
            $this->dispatch('notify', message: __('app.webs.messages.create_disabled'), variant: 'warning', title: __('app.webs.messages.error_title'));
            return;
        }

        $data = $this->validate();

        $web = Web::findOrFail($this->webId);
        $web->update($data);

        $this->dispatch('notify', message: __('app.webs.messages.updated_success'), variant: 'success', title: __('app.webs.messages.success_title'));
        $this->dispatch('close-web-offcanvas');

        $this->reset([
            'webId',
            'client_id',
            'name',
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
            'pagespeed_mobile',
            'pagespeed_desktop',
            'seo_score',
            'priority',
            'notes',
        ]);
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

        $web = Web::find($this->webToDeleteId);
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
            ->with('client:id,name')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('url', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        $clientOptions = Client::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($c) => ['value' => (string) $c->id, 'label' => $c->name])
            ->values()
            ->all();

        return view('livewire.webs.index', [
            'webs' => $webs,
            'clientOptions' => $clientOptions,
        ])->layout('layouts.app');
    }
}
