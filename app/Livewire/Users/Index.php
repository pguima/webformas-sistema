<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Livewire\Concerns\HasViewMode;


class Index extends Component
{
    use WithPagination, HasViewMode;

    // Search
    public $search = '';

    public int $perPage = 10;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    // Form Data
    public $userId;
    public $name;
    public $email;
    public $role = 'Funcionário'; // Placeholder
    public $status = 'Active'; // Placeholder

    public array $prefetchedUsers = [];

    // Delete Data
    public $userToDeleteId;
    public $deleteConfirmation = '';

    public function rules()
    {
        return [
            'name' => 'required|min:3',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'role' => ['required', Rule::in(['SuperAdmin', 'Admin', 'Cliente', 'Funcionário'])],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => __('app.users.validation.email_unique'),
        ];
    }

    public function create()
    {
        $this->reset(['userId', 'name', 'email']);
        $this->role = 'Funcionário';
        $this->status = 'Active';
    }

    public function prefetch($id)
    {
        if (isset($this->prefetchedUsers[$id])) {
            return;
        }

        $user = User::query()
            ->select(['id', 'name', 'email', 'role', 'status'])
            ->find($id);

        if (!$user) {
            return;
        }

        $this->prefetchedUsers[$id] = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
        ];
    }

    public function edit($id)
    {
        if (isset($this->prefetchedUsers[$id])) {
            $this->userId = $id;
            $this->name = $this->prefetchedUsers[$id]['name'] ?? null;
            $this->email = $this->prefetchedUsers[$id]['email'] ?? null;
            $this->role = $this->prefetchedUsers[$id]['role'] ?? 'Funcionário';
            $this->status = $this->prefetchedUsers[$id]['status'] ?? 'Active';
            return;
        }

        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role ?: 'Funcionário';
        $this->status = $user->status;
    }

    public function save()
    {
        $this->validate();

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'status' => $this->status,
            ]);
            $this->dispatch('notify', message: __('app.users.messages.updated_success'), variant: 'success', title: __('app.users.messages.success_title'));
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make(Str::random(64)),
                'role' => $this->role,
                'status' => $this->status,
            ]);

            $status = Password::sendResetLink(['email' => $user->email]);
            if ($status !== Password::RESET_LINK_SENT) {
                $this->dispatch('notify', message: __($status), variant: 'danger', title: __('app.users.messages.error_title'));
                return;
            }

            $this->dispatch('notify', message: __('app.users.messages.created_success'), variant: 'success', title: __('app.users.messages.success_title'));
        }

        $this->dispatch('close-user-offcanvas');
        $this->reset(['userId', 'name', 'email']);
    }

    public function confirmDelete($id)
    {
        $this->userToDeleteId = $id;
        $this->deleteConfirmation = '';
        $this->dispatch('open-delete-modal');
    }

    public function delete()
    {
        if ($this->deleteConfirmation !== 'DELETE') {
            $this->addError('deleteConfirmation', __('app.users.delete.placeholder', ['word' => 'DELETE']));
            return;
        }

        $user = User::find($this->userToDeleteId);
        if ($user) {
            if ($user->isSuperAdmin()) {
                $this->dispatch('notify', message: __('app.users.messages.cannot_delete_superadmin'), variant: 'danger', title: __('app.users.messages.error_title'));
                $this->dispatch('close-delete-modal');
                $this->reset(['userToDeleteId', 'deleteConfirmation']);
                return;
            }

            if (auth()->id() && (int) $user->id === (int) auth()->id()) {
                $this->dispatch('notify', message: __('app.users.messages.cannot_delete_self'), variant: 'danger', title: __('app.users.messages.error_title'));
                $this->dispatch('close-delete-modal');
                $this->reset(['userToDeleteId', 'deleteConfirmation']);
                return;
            }

            $user->delete();
            $this->dispatch('notify', message: __('app.users.messages.deleted_success'), variant: 'success', title: __('app.users.messages.success_title'));
        } else {
            $this->dispatch('notify', message: __('app.users.messages.error_not_found'), variant: 'danger', title: __('app.users.messages.error_title'));
        }

        $this->dispatch('close-delete-modal');
        $this->reset(['userToDeleteId', 'deleteConfirmation']);
    }

    public function exportCsv()
    {
        $filename = 'users.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['id', 'name', 'email', 'role', 'status']);

            User::query()
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%')
                            ->orWhere('role', 'like', '%' . $this->search . '%')
                            ->orWhere('status', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderByDesc('id')
                ->chunk(500, function ($users) use ($handle) {
                    foreach ($users as $user) {
                        fputcsv($handle, [$user->id, $user->name, $user->email, $user->role, $user->status]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('role', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('id')
            ->get(['id', 'name', 'email', 'role', 'status']);

        $pdf = Pdf::loadView('exports.users-pdf', [
            'users' => $users,
            'search' => $this->search,
        ])->setPaper('a4');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'users.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('role', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.users.index', [
            'users' => $users
        ])->layout('layouts.app');
    }
}
