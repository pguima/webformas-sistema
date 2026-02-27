---
description: Scaffold/template de CRUD (padrão Users-like)
---

# Scaffold CRUD (Users-like)

Este scaffold é o padrão oficial para criar qualquer CRUD na **aplicação real** (fora de `/design-system`).

Regras:
- Este template **não** se aplica ao Design System (DS). DS continua sendo **Route::view + Blade anônimo + Alpine**.
- Para CRUDs, manter o padrão de UX e estrutura da página **igual ao CRUD de Users**.

---

## 0) Checklist rápido (Definition of Done)

- [ ] Rota em `routes/web.php` apontando para o Livewire `Index::class`.
- [ ] Classe Livewire com:
  - search + perPage + paginação
  - create/edit/save
  - confirmDelete/delete (com confirmação textual)
  - `dispatch('notify', ...)`
  - fechamento de modal/offcanvas via `dispatch('close-...')`
- [ ] Blade com:
  - header (título/subtítulo + ações)
  - filtros dentro de `<x-ds::card>`
  - listagem table/grid
  - offcanvas create/edit
  - modal delete
  - loading/disabled em botões para evitar duplo clique
- [ ] 100% strings com `__()` e chaves criadas nos arquivos de tradução.

---

## 1) Rotas (`routes/web.php`)

Adicionar dentro do grupo administrativo (exemplo):

```php
Route::middleware(['auth', 'verified', 'role:SuperAdmin,Admin'])->group(function () {
    Route::get('/entities', \App\Livewire\Entities\Index::class);
});
```

Notas:
- Use plural na URL (ex: `/clients`, `/employees`).
- O namespace do Livewire deve seguir o domínio (ex: `App\Livewire\Clients\Index`).

---

## 2) Classe Livewire (`app/Livewire/Entities/Index.php`)

Copie e adapte. Troque:
- `Entity`/`Entities` pelo domínio real
- campos do formulário
- query do `render()`
- regras de negócio do delete

```php
<?php

namespace App\Livewire\Entities;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

use App\Livewire\Concerns\HasViewMode;
use App\Models\Entity;

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
    public ?int $entityId = null;
    public ?string $name = null;
    public ?string $status = 'Active';

    public array $prefetchedEntities = [];

    // Delete
    public ?int $entityToDeleteId = null;
    public string $deleteConfirmation = '';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ];
    }

    public function create(): void
    {
        $this->reset(['entityId', 'name']);
        $this->status = 'Active';
    }

    public function prefetch(int $id): void
    {
        if (isset($this->prefetchedEntities[$id])) {
            return;
        }

        $entity = Entity::query()
            ->select(['id', 'name', 'status'])
            ->find($id);

        if (!$entity) {
            return;
        }

        $this->prefetchedEntities[$id] = [
            'name' => $entity->name,
            'status' => $entity->status,
        ];
    }

    public function edit(int $id): void
    {
        if (isset($this->prefetchedEntities[$id])) {
            $this->entityId = $id;
            $this->name = $this->prefetchedEntities[$id]['name'] ?? null;
            $this->status = $this->prefetchedEntities[$id]['status'] ?? 'Active';
            return;
        }

        $entity = Entity::findOrFail($id);
        $this->entityId = $entity->id;
        $this->name = $entity->name;
        $this->status = $entity->status;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->entityId) {
            $entity = Entity::findOrFail($this->entityId);
            $entity->update([
                'name' => $this->name,
                'status' => $this->status,
            ]);

            $this->dispatch('notify', message: __('app.entities.messages.updated_success'), variant: 'success', title: __('app.entities.messages.success_title'));
        } else {
            Entity::create([
                'name' => $this->name,
                'status' => $this->status,
            ]);

            $this->dispatch('notify', message: __('app.entities.messages.created_success'), variant: 'success', title: __('app.entities.messages.success_title'));
        }

        $this->dispatch('close-entity-offcanvas');
        $this->reset(['entityId', 'name']);
    }

    public function confirmDelete(int $id): void
    {
        $this->entityToDeleteId = $id;
        $this->deleteConfirmation = '';
        $this->dispatch('open-delete-modal');
    }

    public function delete(): void
    {
        if ($this->deleteConfirmation !== 'DELETE') {
            $this->addError('deleteConfirmation', __('app.entities.delete.placeholder', ['word' => 'DELETE']));
            return;
        }

        $entity = Entity::find($this->entityToDeleteId);
        if (!$entity) {
            $this->dispatch('notify', message: __('app.entities.messages.error_not_found'), variant: 'danger', title: __('app.entities.messages.error_title'));
            $this->dispatch('close-delete-modal');
            $this->reset(['entityToDeleteId', 'deleteConfirmation']);
            return;
        }

        $entity->delete();

        $this->dispatch('notify', message: __('app.entities.messages.deleted_success'), variant: 'success', title: __('app.entities.messages.success_title'));
        $this->dispatch('close-delete-modal');
        $this->reset(['entityToDeleteId', 'deleteConfirmation']);
    }

    public function render()
    {
        $entities = Entity::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.entities.index', [
            'entities' => $entities,
        ])->layout('layouts.app');
    }
}
```

Notas:
- Se tiver `unique`, use: `Rule::unique('entities', 'campo')->ignore($this->entityId)`.
- Se existir regra de negócio (ex: não deletar registro default), trate no `delete()` igual o `Users` trata SuperAdmin/self.

---

## 3) View Blade (`resources/views/livewire/entities/index.blade.php`)

Estrutura base (copie e adapte textos/fields):

```blade
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[var(--text-primary)]">{{ __('app.entities.title') }}</h1>
            <p class="mt-1 text-sm text-[var(--text-secondary)]">{{ __('app.entities.subtitle') }}</p>
        </div>

        <div class="flex gap-2">
            <x-ds::button
                type="button"
                icon="solar:add-circle-linear"
                x-on:click="$dispatch('open-create-entity-offcanvas'); $wire.create()"
                wire:loading.attr="disabled"
            >
                {{ __('app.entities.add') }}
            </x-ds::button>
        </div>
    </div>

    <x-ds::card class="mt-6">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full max-w-sm">
                <x-ds::input
                    icon="solar:magnifer-linear"
                    placeholder="{{ __('app.entities.search_placeholder') }}"
                    wire:model.live.debounce.300ms="search"
                />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <div class="w-full sm:w-40">
                    <div class="flex items-center gap-3">
                        <span class="shrink-0 text-sm font-medium text-[var(--text-primary)]">{{ __('app.entities.per_page') }}</span>
                        <x-ds::select
                            wire:model.live="perPage"
                            :options="[
                                ['value' => 10, 'label' => '10'],
                                ['value' => 25, 'label' => '25'],
                                ['value' => 50, 'label' => '50'],
                                ['value' => 100, 'label' => '100'],
                            ]"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-ds::button
                        type="button"
                        size="icon"
                        variant="{{ $viewMode === 'list' ? 'secondary' : 'ghost' }}"
                        icon="solar:list-linear"
                        wire:click="$set('viewMode','list')"
                        wire:loading.attr="disabled"
                    />

                    <x-ds::button
                        type="button"
                        size="icon"
                        variant="{{ $viewMode === 'grid' ? 'secondary' : 'ghost' }}"
                        icon="solar:widget-4-linear"
                        wire:click="$set('viewMode','grid')"
                        wire:loading.attr="disabled"
                    />
                </div>
            </div>
        </div>

        @if ($viewMode === 'grid')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($entities as $entity)
                    <x-ds::card class="relative p-4" wire:key="grid-{{ $entity->id }}">
                        <div class="absolute top-3 right-3 flex items-center gap-2">
                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:pen-linear"
                                wire:mouseenter="prefetch({{ $entity->id }})"
                                x-on:click="$dispatch('open-edit-entity-offcanvas'); $wire.edit({{ $entity->id }})"
                                wire:loading.attr="disabled"
                            />

                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:trash-bin-trash-linear"
                                class="hover:text-[var(--status-error)]"
                                wire:click.prevent="confirmDelete({{ $entity->id }})"
                                wire:loading.attr="disabled"
                            />
                        </div>

                        <div class="text-sm font-semibold text-[var(--text-primary)] truncate">{{ $entity->name }}</div>
                        <div class="mt-3">
                            <x-ds::badge variant="{{ $entity->status === 'Active' ? 'success' : 'secondary' }}" :dot="true">{{ $entity->status }}</x-ds::badge>
                        </div>
                    </x-ds::card>
                @empty
                    <div class="py-8 text-center text-sm text-[var(--text-secondary)] sm:col-span-2 xl:col-span-3">
                        {{ __('app.entities.no_results', ['search' => $search]) }}
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $entities->links() }}
            </div>
        @else
            <x-ds::table :headers="[__('app.entities.table.name'), __('app.entities.table.status'), __('app.entities.table.actions')]">
                @forelse($entities as $entity)
                    <tr class="border-b border-[var(--border-subtle)] transition-colors hover:bg-[var(--surface-hover)]" wire:key="{{ $entity->id }}">
                        <x-ds::table-cell>
                            <div class="text-sm font-medium text-[var(--text-primary)]">{{ $entity->name }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <x-ds::badge variant="{{ $entity->status === 'Active' ? 'success' : 'secondary' }}" :dot="true">{{ $entity->status }}</x-ds::badge>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="flex items-center gap-2">
                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:pen-linear"
                                    wire:mouseenter="prefetch({{ $entity->id }})"
                                    x-on:click="$dispatch('open-edit-entity-offcanvas'); $wire.edit({{ $entity->id }})"
                                    wire:loading.attr="disabled"
                                />
                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:trash-bin-trash-linear"
                                    class="hover:text-[var(--status-error)]"
                                    wire:click.prevent="confirmDelete({{ $entity->id }})"
                                    wire:loading.attr="disabled"
                                />
                            </div>
                        </x-ds::table-cell>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-8 text-center text-sm text-[var(--text-secondary)]">
                            {{ __('app.entities.no_results', ['search' => $search]) }}
                        </td>
                    </tr>
                @endforelse

                <x-slot:footer>
                    <div class="mt-4">
                        {{ $entities->links() }}
                    </div>
                </x-slot:footer>
            </x-ds::table>
        @endif
    </x-ds::card>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-create-entity-offcanvas.window="open = true"
        x-on:close-entity-offcanvas.window="open = false"
        title="{{ __('app.entities.offcanvas.create_title') }}"
        description="{{ __('app.entities.offcanvas.create_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.entities.form.name') }}" wire:model="name" required :error="$errors->first('name')" />
            <x-ds::select label="{{ __('app.entities.form.status') }}" wire:model="status" :options="['Active' => 'Active', 'Inactive' => 'Inactive']" />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.entities.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.entities.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.entities.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-edit-entity-offcanvas.window="open = true"
        x-on:close-entity-offcanvas.window="open = false"
        title="{{ __('app.entities.offcanvas.edit_title') }}"
        description="{{ __('app.entities.offcanvas.edit_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.entities.form.name') }}" wire:model="name" required :error="$errors->first('name')" />
            <x-ds::select label="{{ __('app.entities.form.status') }}" wire:model="status" :options="['Active' => 'Active', 'Inactive' => 'Inactive']" />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.entities.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.entities.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.entities.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::modal
        x-on:open-delete-modal.window="openModal()"
        x-on:close-delete-modal.window="closeModal()"
        title="{{ __('app.entities.delete.title') }}"
        size="md"
    >
        <div class="space-y-4">
            <x-ds::alert variant="danger" icon="solar:danger-triangle-linear">
                {{ __('app.entities.delete.warning') }}
            </x-ds::alert>

            <p class="text-sm text-[var(--text-secondary)]">
                {!! __('app.entities.delete.confirm_help', ['word' => '<span class="select-all font-mono font-bold text-[var(--status-error)]">DELETE</span>']) !!}
            </p>

            <x-ds::input
                wire:model.live="deleteConfirmation"
                placeholder="{{ __('app.entities.delete.placeholder', ['word' => 'DELETE']) }}"
                class="border-[var(--status-error)] focus:border-[var(--status-error)] focus:ring-[var(--status-error)]/20"
            />
            @error('deleteConfirmation') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.entities.form.cancel') }}</x-ds::button>
                <x-ds::button variant="danger" icon="solar:trash-bin-trash-linear" wire:click.prevent="delete" wire:loading.attr="disabled">
                    {{ __('app.entities.delete.delete_permanently') }}
                </x-ds::button>
            </div>
        </x-slot:footer>
    </x-ds::modal>
</div>
```

Notas:
- Os nomes dos eventos (`open-create-entity-offcanvas`, etc.) são parte do padrão.
- Se a entidade não tiver `grid`, você pode remover o modo grid — mas o default do projeto suporta (via `HasViewMode`).

---

## 4) i18n (chaves sugeridas)

Criar chaves equivalentes em `resources/lang/{locale}/app.php` (ou no arquivo de domínio que você já usa para aquela feature).

Exemplo de chaves mínimas:

```php
return [
    'entities' => [
        'title' => 'Entities',
        'subtitle' => 'Manage your entities',
        'add' => 'Add entity',
        'search_placeholder' => 'Search...',
        'per_page' => 'Per page',
        'no_results' => 'No results for ":search".',

        'table' => [
            'name' => 'Name',
            'status' => 'Status',
            'actions' => 'Actions',
        ],

        'offcanvas' => [
            'create_title' => 'Create entity',
            'create_description' => 'Fill the details to create a new entity.',
            'edit_title' => 'Edit entity',
            'edit_description' => 'Update the entity details.',
        ],

        'form' => [
            'name' => 'Name',
            'status' => 'Status',
            'cancel' => 'Cancel',
            'save' => 'Save',
        ],

        'delete' => [
            'title' => 'Delete entity',
            'warning' => 'This action cannot be undone.',
            'confirm_help' => 'Type :word to confirm.',
            'placeholder' => 'Type :word to confirm',
            'delete_permanently' => 'Delete permanently',
        ],

        'messages' => [
            'success_title' => 'Success',
            'error_title' => 'Error',
            'created_success' => 'Entity created successfully.',
            'updated_success' => 'Entity updated successfully.',
            'deleted_success' => 'Entity deleted successfully.',
            'error_not_found' => 'Entity not found.',
        ],
    ],
];
```

---

## 5) Onde encaixar arquivos

- Livewire:
  - `app/Livewire/Entities/Index.php`
  - `resources/views/livewire/entities/index.blade.php`
- Model:
  - `app/Models/Entity.php`
- Migração:
  - `database/migrations/..._create_entities_table.php`
- Traduções:
  - `resources/lang/en/app.php` (ou arquivo de domínio)
  - `resources/lang/pt_BR/app.php`
  - `resources/lang/es/app.php`
