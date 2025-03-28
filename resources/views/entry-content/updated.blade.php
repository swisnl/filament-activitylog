<x-filament-activitylog::entry-content-container :label="__('filament-activitylog::activitylog.events.updated.label')">
    <x-filament-activitylog::attributes-table :record="$record->subject" :newAttributes="$record->properties['attributes']" :oldAttributes="$record->properties['old']" />
</x-filament-activitylog::entry-content-container>
