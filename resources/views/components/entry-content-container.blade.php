<div class="flex flex-col gap-y-4 pt-4">
    @if (!empty($label))
        <p class="italic text-sm">{{ $label }}</p>
    @endif

    @if (! \Filament\Support\is_slot_empty($slot))
        <div>
            {{ $slot }}
        </div>
    @endif
</div>
