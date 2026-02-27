# Skills / Regras do Projeto (Guardrails)

Este arquivo define as **regras obrigatórias** e o **padrão de implementação** que devem ser seguidos ao criar ou atualizar funcionalidades neste projeto.

---

## 1) Escopos do projeto (regra #1)

### 1.1 Design System (DS) = `/design-system/*`
- Rotas **somente** via `Route::view()`.
- **Proibido** criar Controllers, Actions, Services ou Classes Livewire dentro do escopo do DS.
- Interatividade no DS:
  - Preferir **Alpine.js**.
  - Pode usar recursos nativos do Livewire no Blade quando fizer sentido (ex: `wire:navigate`), **sem** criar componentes Livewire para DS.
- Componentes do DS:
  - Blade components **anônimos** em `resources/views/design-system/components/`.
- Páginas do DS:
  - `resources/views/design-system/pages/*.blade.php`.
- Internacionalização (DS):
  - 100% das strings com `__()`.
  - Traduções do DS centralizadas em `resources/lang/en/ds.php` (base) + overrides em `pt_BR/ds.php` e `es/ds.php`.

### 1.2 Aplicação real (fora do DS)
- Pode usar classes Livewire, Models, validações, regras, etc.
- **CRUDs devem seguir o padrão do CRUD de Users** (ver seção 3).

---

## 2) UI/UX + Estilo (Design Tokens)

### 2.1 Tokens obrigatórios
- Cores, bordas, sombras e estados devem usar **CSS Variables** (Design Tokens) definidas no projeto.
- Preferir sempre `text-[var(--...)]`, `bg-[var(--...)]`, `border-[var(--...)]` (ou a forma padronizada que o projeto já estiver usando).

### 2.2 Estados e micro-interações
- Todo elemento interativo deve ter estados coerentes:
  - `hover`, `active`, `focus`, `disabled`, `loading`.
- Para ações assíncronas:
  - Evitar duplo clique (ex: `wire:loading.attr="disabled"` + `wire:target="..."`).

### 2.3 Componentização
- Antes de criar HTML novo, procurar componente existente em `resources/views/design-system/components/`.
- Links devem usar **sempre** `<x-ds::link>` nas páginas do DS e exemplos.
- Botões devem usar **sempre** `<x-ds::button>`.
- Feedback deve usar `<x-ds::alert>`, `<x-ds::toast>`, `<x-ds::modal>`, `<x-ds::offcanvas>`.

---

## 3) Padrão de CRUD (obrigatório) — “Users-like”

Este padrão é a referência para qualquer nova página com CRUD (ex: Clients, Employees, Files, etc.).

### 3.1 Rotas
- CRUDs administrativos devem ficar no grupo com middleware (exemplo):
  - `Route::middleware(['auth', 'verified', 'role:SuperAdmin,Admin'])->group(...)`
- A rota deve apontar para o componente Livewire:
  - `Route::get('/entidade', \App\Livewire\Entidade\Index::class);`

### 3.2 Estrutura do componente Livewire (classe)
**Referência**: `app/Livewire/Users/Index.php`

Obrigatório:
- Estado (public props) separado por intenção:
  - **Busca/paginação**: `search`, `perPage`, `updatedSearch()`, `updatedPerPage()`.
  - **Form**: `id` (ex: `userId`) + campos (`name`, `email`, etc.).
  - **Delete**: `*_ToDeleteId`, `deleteConfirmation`.
- `rules()` e (quando necessário) `messages()`.
- Métodos padrão:
  - `create()`
  - `prefetch($id)` (para abrir edição/offcanvas sem “lag”) 
  - `edit($id)`
  - `save()` (cria/atualiza)
  - `confirmDelete($id)`
  - `delete()`
- Feedback padronizado:
  - `dispatch('notify', message: __('...'), variant: 'success|danger|warning|info', title: __('...'))`
- Fechamento de UI:
  - `dispatch('close-...')` para offcanvas/modal.
- `render()` retorna view e define layout:
  - `->layout('layouts.app')`

Regras importantes:
- Sempre usar `validate()` antes de persistir.
- Para uniqueness:
  - `Rule::unique('tabela')->ignore($this->id)`
- Proteger regras de negócio básicas (exemplo do Users):
  - não deletar SuperAdmin
  - não deletar a si mesmo

### 3.3 Estrutura da view (Blade)
**Referência**: `resources/views/livewire/users/index.blade.php`

Obrigatório:
- Header padrão:
  - Título + subtítulo via i18n.
  - Ações à direita com `<x-ds::button>`.
- Card para filtros e listagem:
  - Busca com `<x-ds::input>` usando `wire:model.live.debounce.300ms`.
  - `perPage` com `<x-ds::select>`.
- Modos de visualização (quando aplicável):
  - List/Grid com trait `HasViewMode`.
- Listagem:
  - Lista: `<x-ds::table>`.
  - Grid: cards com ações rápidas.
- Ações (editar/deletar):
  - Botões `ghost` `size="icon"`.
  - Prefetch no hover (`wire:mouseenter="prefetch(id)"`) quando fizer sentido.
- Create/Edit:
  - Usar `<x-ds::offcanvas>`.
  - Form com `wire:submit.prevent="save"`.
  - Erros com `<x-ds::alert variant="danger">{{ $errors->first() }}</x-ds::alert>`.
  - Botão submit:
    - `wire:loading.attr="disabled" wire:target="save"`.
- Delete:
  - Usar `<x-ds::modal>` com confirmação textual (ex: digitar `DELETE`).

### 3.4 i18n para CRUD
- Todas as labels, placeholders, títulos, mensagens e textos devem vir de `resources/lang/{locale}/app.php` (ou arquivo de domínio equivalente existente no projeto).
- Mensagens de sucesso/erro devem ter chave própria.

---

## 4) Checklist (antes de finalizar uma entrega)

- [ ] Estou no escopo certo? (DS x App)
- [ ] DS: rotas `Route::view`, sem classes Livewire/controllers.
- [ ] 100% strings com `__()`.
- [ ] Usei componentes do DS ao invés de HTML “solto”.
- [ ] CRUD: segue padrão Users-like (offcanvas, modal delete, search/perPage, notify, loading disable).
- [ ] Estados de loading/disabled evitam duplo clique.
- [ ] Tokens visuais via CSS variables.

---

## 5) Convenções de nomenclatura sugeridas

- Livewire:
  - `App\Livewire\{Domain}\Index`
  - `resources/views/livewire/{domain}/index.blade.php`
- Eventos de UI:
  - `open-create-...-offcanvas`
  - `open-edit-...-offcanvas`
  - `close-...-offcanvas`
  - `open-delete-modal`
  - `close-delete-modal`

---

## 6) Anti-alucinação / dependências
- Não inventar bibliotecas externas.
- Manter-se em Laravel 12 + Livewire 4 + Alpine + Tailwind + Larapex + Iconify (conforme o projeto já usa).
