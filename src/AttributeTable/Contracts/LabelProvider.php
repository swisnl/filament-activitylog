<?php

namespace Swis\Filament\ActivityLog\AttributeTable\Contracts;

interface LabelProvider
{
    public function getAttributeTableLabel(string $key, string $recordClass): ?string;
}
