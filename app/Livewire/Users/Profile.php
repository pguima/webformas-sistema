<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';

    /** @var TemporaryUploadedFile|null */
    public $avatar = null;

    public ?string $current_password = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;

    public bool $saved = false;
    public bool $passwordSaved = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->name = (string) $user->name;
        $this->email = (string) $user->email;
    }

    public function saveProfile(): void
    {
        $this->saved = false;

        /** @var User $user */
        $user = auth()->user();

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['avatar'])) {
            $newPath = $data['avatar']->store('avatars', 'public');

            $oldPath = $user->avatar_path;
            $user->avatar_path = $newPath;

            if ($oldPath && str_starts_with($oldPath, 'avatars/')) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $user->save();

        $this->avatar = null;
        $this->saved = true;

        $this->dispatch('profile-saved');
        $this->dispatch('notify', message: __('app.profile.updated'), variant: 'success', title: __('app.users.messages.success_title'));
    }

    public function savePassword(): void
    {
        $this->passwordSaved = false;

        /** @var User $user */
        $user = auth()->user();

        $this->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check((string) $this->current_password, (string) $user->password)) {
            $this->addError('current_password', __('app.profile.current_password_incorrect'));
            return;
        }

        $user->password = Hash::make((string) $this->password);
        $user->save();

        $this->current_password = null;
        $this->password = null;
        $this->password_confirmation = null;
        $this->passwordSaved = true;

        $this->dispatch('password-saved');
        $this->dispatch('notify', message: __('app.profile.password_updated'), variant: 'success', title: __('app.users.messages.success_title'));
    }

    public function render()
    {
        return view('livewire.users.profile', [
            'user' => auth()->user(),
        ])->layout('layouts.app');
    }
}
