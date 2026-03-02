<?php

namespace App\Livewire\Widgets;

use App\Livewire\Concerns\HasViewMode;
use App\Models\Widget;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, HasViewMode, WithFileUploads;

    private const VIEW_MODE_SESSION_KEY = 'viewMode.widgets';

    public string $search = '';

    public int $perPage = 10;

    public ?int $widgetId = null;

    public ?string $name = null;

    public ?string $author = null;

    public ?string $category = null;

    /** @var TemporaryUploadedFile|null */
    public $image = null;

    public ?string $json_code = null;

    public array $prefetchedWidgets = [];

    public ?int $widgetToDeleteId = null;

    public string $deleteConfirmation = '';

    public function mount(): void
    {
        $this->mountHasViewMode();
    }

    public function mountHasViewMode(): void
    {
        $this->viewMode = Session::get(self::VIEW_MODE_SESSION_KEY, 'grid');
    }

    public function updatedViewMode($value): void
    {
        Session::put(self::VIEW_MODE_SESSION_KEY, $value);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public static function categoryOptions(): array
    {
        return [
            'Hero' => 'Hero',
            'Header' => 'Header',
            'Footer' => 'Footer',
            'Botão' => 'Botão',
            'FAQ' => 'FAQ',
            'Forms' => 'Forms',
            'Faixa' => 'Faixa',
        ];
    }

    public function rules(): array
    {
        $categories = array_keys(self::categoryOptions());

        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'author' => ['required', 'string', 'min:2', 'max:255'],
            'category' => ['required', Rule::in($categories)],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'json_code' => ['required', 'string', 'json'],
        ];
    }

    public function create(): void
    {
        $this->reset(['widgetId', 'name', 'author', 'category', 'image', 'json_code']);
        $this->category = array_key_first(self::categoryOptions());
        $this->json_code = "{}";
    }

    public function prefetch(int $id): void
    {
        if (isset($this->prefetchedWidgets[$id])) {
            return;
        }

        $widget = Widget::query()
            ->select(['id', 'name', 'author', 'category', 'image_path'])
            ->find($id);

        if (! $widget) {
            return;
        }

        $this->prefetchedWidgets[$id] = [
            'name' => $widget->name,
            'author' => $widget->author,
            'category' => $widget->category,
            'image_path' => $widget->image_path,
        ];
    }

    public function edit(int $id): void
    {
        if (isset($this->prefetchedWidgets[$id])) {
            $this->widgetId = $id;
            $this->name = $this->prefetchedWidgets[$id]['name'] ?? null;
            $this->author = $this->prefetchedWidgets[$id]['author'] ?? null;
            $this->category = $this->prefetchedWidgets[$id]['category'] ?? array_key_first(self::categoryOptions());
            $this->image = null;

            $widget = Widget::query()->select(['id', 'json_code'])->find($id);
            if ($widget) {
                $this->json_code = json_encode($widget->json_code ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            } else {
                $this->json_code = "{}";
            }

            return;
        }

        $widget = Widget::findOrFail($id);

        $this->widgetId = $widget->id;
        $this->name = $widget->name;
        $this->author = $widget->author;
        $this->category = $widget->category;
        $this->image = null;
        $this->json_code = json_encode($widget->json_code ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function save(): void
    {
        $data = $this->validate();

        $json = json_decode((string) $data['json_code'], true);
        if (! is_array($json)) {
            $this->addError('json_code', __('app.widgets.validation.json_invalid'));
            return;
        }

        if ($this->widgetId) {
            $widget = Widget::findOrFail($this->widgetId);

            if (! empty($data['image'])) {
                $newPath = $data['image']->store('widgets', 'public');
                $oldPath = $widget->image_path;
                $widget->image_path = $newPath;

                if ($oldPath && str_starts_with($oldPath, 'widgets/')) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $widget->update([
                'name' => $data['name'],
                'author' => $data['author'],
                'category' => $data['category'],
                'json_code' => $json,
            ]);

            $this->dispatch('notify', message: __('app.widgets.messages.updated_success'), variant: 'success', title: __('app.widgets.messages.success_title'));
        } else {
            $imagePath = null;
            if (! empty($data['image'])) {
                $imagePath = $data['image']->store('widgets', 'public');
            }

            Widget::create([
                'name' => $data['name'],
                'author' => $data['author'],
                'category' => $data['category'],
                'image_path' => $imagePath,
                'json_code' => $json,
            ]);

            $this->dispatch('notify', message: __('app.widgets.messages.created_success'), variant: 'success', title: __('app.widgets.messages.success_title'));
        }

        $this->dispatch('close-widget-offcanvas');
        $this->reset(['widgetId', 'name', 'author', 'category', 'image', 'json_code']);
        $this->prefetchedWidgets = [];
    }

    public function confirmDelete(int $id): void
    {
        $this->widgetToDeleteId = $id;
        $this->deleteConfirmation = '';
        $this->dispatch('open-delete-modal');
    }

    public function delete(): void
    {
        if ($this->deleteConfirmation !== 'DELETE') {
            $this->addError('deleteConfirmation', __('app.widgets.delete.placeholder', ['word' => 'DELETE']));
            return;
        }

        $widget = Widget::find($this->widgetToDeleteId);
        if (! $widget) {
            $this->dispatch('notify', message: __('app.widgets.messages.error_not_found'), variant: 'danger', title: __('app.widgets.messages.error_title'));
            $this->dispatch('close-delete-modal');
            $this->reset(['widgetToDeleteId', 'deleteConfirmation']);
            return;
        }

        $imagePath = $widget->image_path;

        $widget->delete();

        if ($imagePath && str_starts_with($imagePath, 'widgets/')) {
            Storage::disk('public')->delete($imagePath);
        }

        $this->dispatch('notify', message: __('app.widgets.messages.deleted_success'), variant: 'success', title: __('app.widgets.messages.success_title'));
        $this->dispatch('close-delete-modal');
        $this->reset(['widgetToDeleteId', 'deleteConfirmation']);
    }

    public function copyJson($id): void
    {
        $idInt = filter_var($id, FILTER_VALIDATE_INT);

        if ($idInt === false && preg_match('/(\d+)/', (string) $id, $matches) === 1) {
            $idInt = filter_var($matches[1], FILTER_VALIDATE_INT);
        }

        if ($idInt === false) {
            $this->dispatch('notify', message: __('app.widgets.messages.error_not_found'), variant: 'danger', title: __('app.widgets.messages.error_title'));
            return;
        }

        $widget = Widget::query()->select(['id', 'json_code'])->find((int) $idInt);

        if (! $widget) {
            $this->dispatch('notify', message: __('app.widgets.messages.error_not_found'), variant: 'danger', title: __('app.widgets.messages.error_title'));
            return;
        }

        $json = json_encode($widget->json_code ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($json === false) {
            $json = '{}';
        }

        $this->dispatch('widgets-copy-json', json: $json);
    }

    public function toJSON($id): void
    {
        $this->copyJson($id);
    }

    public function render()
    {
        $widgets = Widget::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('author', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.widgets.index', [
            'widgets' => $widgets,
            'categoryOptions' => self::categoryOptions(),
        ])->layout('layouts.app');
    }
}
