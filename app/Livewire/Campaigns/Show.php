<?php

namespace App\Livewire\Campaigns;

use App\Models\Campaign;
use Livewire\Component;

class Show extends Component
{
    public Campaign $campaign;

    public function mount(Campaign $campaign): void
    {
        $this->campaign = $campaign;
    }

    public function render()
    {
        return view('livewire.campaigns.show', [
            'campaign' => $this->campaign,
        ])->layout('layouts.app');
    }
}
