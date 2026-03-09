<x-ds::card
    title="{{ __('app.campaigns.profile_card.title') }}"
    description="{{ __('app.campaigns.profile_card.description') }}"
>
    <form wire:submit.prevent="save" class="space-y-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-ds::input
                label="{{ __('app.campaigns.fields.manager_customer_id') }}"
                wire:model="manager_customer_id"
                :error="$errors->first('manager_customer_id')"
            />

            <x-ds::input
                label="{{ __('app.campaigns.fields.client_customer_id') }}"
                wire:model="client_customer_id"
                :error="$errors->first('client_customer_id')"
            />
        </div>

        <div class="flex justify-end">
            <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">{{ __('app.campaigns.form.save') }}</span>
                <span wire:loading wire:target="save">{{ __('app.campaigns.form.save') }}</span>
            </x-ds::button>
        </div>
    </form>
</x-ds::card>
