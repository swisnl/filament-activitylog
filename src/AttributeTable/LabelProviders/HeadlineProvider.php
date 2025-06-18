<?php

namespace Swis\Filament\ActivityLog\AttributeTable\LabelProviders;

use Illuminate\Support\Str;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\LabelProvider;

class HeadlineProvider implements LabelProvider
{
    public function getAttributeTableLabel(string $key, string $recordClass): ?string
    {
        return Str::headline($key);
    }
}
