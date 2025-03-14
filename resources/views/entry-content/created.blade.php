<x-filament-activitylog::entry-content-container :label="__('filament-activitylog::activitylog.events.created.label')">
    <x-filament-activitylog-attributes-table :record="$record->subject" :newAttributes="$record->properties['attributes']" />
</x-filament-activitylog::entry-content-container>
