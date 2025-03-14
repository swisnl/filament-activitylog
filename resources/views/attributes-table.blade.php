<div class="divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 w-full dark:divide-white/10 dark:border-t-white/10 dark:bg-white/5 dark:ring-white/10">
    <table class="w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
        <thead class="divide-y divide-gray-200 dark:divide-white/5">
        <tr class="bg-gray-50 dark:bg-white/5">
            <th class="px-2 py-1.5 sm:ps-4 text-start text-sm font-semibold text-gray-950 dark:text-white">{{ __('filament-activitylog::activitylog.attributes_table.columns.attribute') }}</th>
            @if ($showOld)
                <th class="px-2 py-1.5 text-start text-sm font-semibold text-gray-950 dark:text-white">{{ __('filament-activitylog::activitylog.attributes_table.columns.old_value') }}</th>
                <th class="px-2 py-1.5 sm:pe-4 text-start text-sm font-semibold text-gray-950 dark:text-white">{{ __('filament-activitylog::activitylog.attributes_table.columns.new_value') }}</th>
            @else
                <th class="px-2 py-1.5 sm:pe-4 text-start text-sm font-semibold text-gray-950 dark:text-white">{{ __('filament-activitylog::activitylog.attributes_table.columns.value') }}</th>
            @endif
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
        @foreach($rows as $row)
            <tr>
                <td class="px-2 py-1.5 sm:ps-4 text-start text-sm text-wrap">{{ $row->getLabel() }}</td>
                @if ($showOld)
                    <td class="px-2 py-1.5 text-start text-sm text-wrap">{{ $row->getOldValue() }}</td>
                @endif
                <td class="px-2 py-1.5 sm:pe-4 text-start text-sm text-wrap">{{ $row->getValue() }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
