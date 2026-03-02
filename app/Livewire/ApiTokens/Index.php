<?php

namespace App\Livewire\ApiTokens;

use App\Models\ApiToken;
use Livewire\Component;

class Index extends Component
{
    public string $name = '';

    public string $abilitiesText = '';

    public ?string $plainToken = null;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'abilitiesText' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function create(): void
    {
        $this->reset(['name', 'abilitiesText', 'plainToken']);
    }

    public function store(): void
    {
        $data = $this->validate();

        $token = bin2hex(random_bytes(32));

        $abilities = collect(explode(',', $data['abilitiesText'] ?? ''))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->values()
            ->all();

        ApiToken::create([
            'name' => $data['name'],
            'token_hash' => hash('sha256', $token),
            'abilities' => $abilities ?: null,
        ]);

        $this->plainToken = $token;

        $this->dispatch('notify', message: __('app.api_tokens.messages.created_success'), variant: 'success', title: __('app.api_tokens.messages.success_title'));
    }

    public function delete(int $id): void
    {
        $token = ApiToken::find($id);
        if (! $token) {
            $this->dispatch('notify', message: __('app.api_tokens.messages.error_not_found'), variant: 'danger', title: __('app.api_tokens.messages.error_title'));
            return;
        }

        $token->delete();

        $this->dispatch('notify', message: __('app.api_tokens.messages.deleted_success'), variant: 'success', title: __('app.api_tokens.messages.success_title'));
    }

    public function render()
    {
        $tokens = ApiToken::query()->latest()->get();

        return view('livewire.api-tokens.index', [
            'tokens' => $tokens,
        ])->layout('layouts.app');
    }
}
