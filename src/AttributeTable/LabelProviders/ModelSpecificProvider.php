<?php

namespace Swis\Filament\Activitylog\AttributeTable\LabelProviders;

use Swis\Filament\Activitylog\AttributeTable\Contracts\LabelProvider;

class ModelSpecificProvider implements LabelProvider
{
    public function getAttributeTableLabel(string $key, string $recordClass): ?string
    {
        if (! is_a($recordClass, LabelProvider::class, true)) {
            return null;
        }

        $instance = new $recordClass;

        return $instance->getAttributeTableLabel($key, $recordClass);
    }
}
