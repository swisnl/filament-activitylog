<x-filament-activitylog::entry-content-container :label="__('filament-activitylog::activitylog.events.updated.label')">
    @if (isset($record->properties['attributes']))
        @if (isset($record->properties['old']))
            <x-filament-activitylog::attributes-table :record="$record->subject" :newAttributes="$record->properties['attributes']" :oldAttributes="$record->properties['old']" />
        @else
            <x-filament-activitylog::attributes-table :record="$record->subject" :newAttributes="$record->properties['attributes']" />
        @endif
    @endif
</x-filament-activitylog::entry-content-container>
