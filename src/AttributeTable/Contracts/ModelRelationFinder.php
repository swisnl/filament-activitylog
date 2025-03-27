<?php

namespace Swis\Filament\Activitylog\AttributeTable\Contracts;

interface ModelRelationFinder
{
    /**
     * Get the BelongsTo relations for a model.
     *
     * The return value is an array where the keys are the foreign key names and the values are the relation method
     * names. MorphTo relations are also considered BelongsTo relations.
     *
     * @return array<string, string>
     */
    public function getBelongsToRelationsForModel(string $modelClass): array;

    /**
     * Get the BelongsTo relation for a model and key.
     *
     * The return value is the relation method name or null if the key is not a BelongsTo relation.
     */
    public function getBelongsToRelationForModelAndKey(string $modelClass, string $key): ?string;

    /**
     * Get the MorphTo relations for a model.
     *
     * The return value is an array where the keys are the morph type column names and the values are the relation
     * method names.
     *
     * @return array<string, string>
     */
    public function getMorphToRelationsForModel(string $modelClass): array;

    /**
     * Check if a key is the type property of a MorphTo relation for a model.
     */
    public function isMorphTypeKey(string $modelClass, string $key): bool;
}
