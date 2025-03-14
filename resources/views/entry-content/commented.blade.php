<x-filament-activitylog::entry-content-container>
    <div class="text-sm leading-6">
        {{ \Filament\Support\Markdown::block($record->properties['comment']) }}
    </div>
</x-filament-activitylog::entry-content-container>
