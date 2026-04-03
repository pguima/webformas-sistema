<?php

namespace App\Livewire\Webs;

use App\Livewire\Concerns\HasViewMode;
use App\Models\Client;
use App\Models\Web;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, HasViewMode;

    #[On('client-webs-refresh')]
    public function refreshList(): void
    {
        // no-op: força re-render após atualização de pagespeed
    }

    public string $search = '';

    public ?int $auditWebId = null;

    public ?int $pagespeedWebId = null;

    public bool $pagespeedOffcanvasOpen = false;

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

    public ?string $gtm_analytics = null;

    public $pagespeed_mobile = null;

    public $pagespeed_desktop = null;

    public $seo_score = null;

    public ?string $notes = null;

    /** @var array<int, array<string, mixed>> */
    public array $prefetchedWebs = [];

    // Delete
    public ?int $webToDeleteId = null;

    public string $deleteConfirmation = '';

    public function audit(int $id): void
    {
        $web = Web::query()->select(['id'])->find($id);

        if (!$web) {
            return;
        }

        $this->auditWebId = $web->id;
        $this->dispatch('open-web-audit-offcanvas');
    }

    public function pagespeed(int $id): void
    {
        $web = Web::query()->select(['id'])->find($id);

        if (!$web) {
            return;
        }

        $this->pagespeedWebId = $web->id;
        $this->pagespeedOffcanvasOpen = true;
        $this->dispatch('open-web-pagespeed-offcanvas');
    }

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
            'gtm_analytics' => ['nullable', 'string', 'max:255'],

            'pagespeed_mobile' => ['nullable', 'integer', 'min:0', 'max:100'],
            'pagespeed_desktop' => ['nullable', 'integer', 'min:0', 'max:100'],
            'seo_score' => ['nullable', 'integer', 'min:0', 'max:100'],

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
                'gtm_analytics',
                'pagespeed_mobile',
                'pagespeed_desktop',
                'seo_score',
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
            'gtm_analytics' => $web->gtm_analytics,
            'pagespeed_mobile' => $web->pagespeed_mobile,
            'pagespeed_desktop' => $web->pagespeed_desktop,
            'seo_score' => $web->seo_score,
            'notes' => $web->notes,
        ];
    }

    public function edit(int $id): void
    {
        if (isset($this->prefetchedWebs[$id])) {
            $this->webId = $id;
            $this->fill($this->prefetchedWebs[$id]);

            $this->platform = $this->platform ?: 'WordPress';
            $this->status = $this->status ?: 'Ativo';
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
        $this->platform = $web->platform ?: 'WordPress';
        $this->status = $web->status ?: 'Ativo';
        $this->responsible = $web->responsible;
        $this->gtm_analytics = $web->gtm_analytics;
        $this->pagespeed_mobile = $web->pagespeed_mobile;
        $this->pagespeed_desktop = $web->pagespeed_desktop;
        $this->seo_score = $web->seo_score;
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

        $this->prefetchedWebs = [];

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
            'gtm_analytics',
            'pagespeed_mobile',
            'pagespeed_desktop',
            'seo_score',
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
                    ->orWhere('url', 'like', '%' . $this->search . '%')
                    ->orWhere('platform', 'like', '%' . $this->search . '%')
                    ->orWhere('responsible', 'like', '%' . $this->search . '%');
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
