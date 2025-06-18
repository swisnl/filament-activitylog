<?php

namespace Swis\Filament\ActivityLog\AttributeTable\LabelProviders;

use Swis\Filament\ActivityLog\AttributeTable\Contracts\LabelProvider;

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
