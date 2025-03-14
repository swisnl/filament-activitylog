<div class="fi-activitylog-container flex flex-col gap-y-8">
    @if ($this->canComment())
        <div class="fi-activitylog-form flex flex-col gap-y-4">
            {{ $this->form }}

            <x-filament::actions>
                <x-filament::button
                    type="submit"
                    variant="primary"
                    wire:click.prevent="saveComment"
                >
                    {{ __('filament-activitylog::activitylog.form.buttons.save') }}
                </x-filament::button>
            </x-filament::actions>
        </div>
    @endif

    <div class="fi-activitylog-table">
        {{ $this->table }}
    </div>
</div>
