<?php

namespace Swis\Filament\Activitylog\AttributeTable\LabelProviders;

use Illuminate\Support\Str;
use Swis\Filament\Activitylog\AttributeTable\Contracts\LabelProvider;

class HeadlineProvider implements LabelProvider
{
    public function getAttributeTableLabel(string $key, string $recordClass): ?string
    {
        return Str::headline($key);
    }
}
