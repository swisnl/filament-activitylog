<?php

namespace Swis\Filament\Activitylog\AttributeTable\LabelProviders;

use Illuminate\Support\Str;

class HeadlineProvider extends BaseLabelProvider
{
    public function getAttributeTableLabel(string $key, string $recordClass): ?string
    {
        return Str::headline($key);
    }
}
