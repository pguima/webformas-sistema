<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class Toaster extends Component
{
    public array $toasts = [];

    #[On('notify')]
    public function notify($message, $variant = 'info', $title = null)
    {
        $id = uniqid();
        $this->toasts[] = [
            'id' => $id,
            'variant' => $variant,
            'title' => $title,
            'message' => $message,
        ];
    }

    public function remove($id)
    {
        $this->toasts = array_values(array_filter($this->toasts, fn($toast) => $toast['id'] !== $id));
    }

    public function render()
    {
        return view('livewire.toaster');
    }
}
