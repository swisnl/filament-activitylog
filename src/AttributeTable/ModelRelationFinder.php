<?php

namespace Swis\Filament\ActivityLog\AttributeTable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use ReflectionClass;
use ReflectionNamedType;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\ModelRelationFinder as ModelRelationFinderContract;

class ModelRelationFinder implements ModelRelationFinderContract
{
    /**
     * @var array<string, array<string, string>>
     */
    protected array $belongsToRelations = [];

    /**
     * @var array<string, array<string, string>>
     */
    protected array $morphToRelations = [];

    /**
     * Build the belongsTo and morphTo relations for a model.
     */
    protected function buildRelationsForModel(string $modelClass): void
    {
        $this->belongsToRelations[$modelClass] = [];
        $this->morphToRelations[$modelClass] = [];

        if (! class_exists($modelClass) || ! is_subclass_of($modelClass, Model::class)) {
            return;
        }

        $reflection = new ReflectionClass($modelClass);
        $methods = $reflection->getMethods();
        $instance = new $modelClass;

        foreach ($methods as $method) {
            if (! $method->isPublic() || $method->isStatic()) {
                continue;
            }

            $methodName = $method->getName();
            $returnType = $method->getReturnType();

            if (! $returnType instanceof ReflectionNamedType || $returnType->isBuiltin() || ! is_a($returnType->getName(), BelongsTo::class, true)) {
                continue;
            }

            $relation = $instance->$methodName();
            if (! $relation instanceof BelongsTo) {
                continue;
            }

            $this->belongsToRelations[$modelClass][$relation->getForeignKeyName()] = $methodName;

            if ($relation instanceof MorphTo) {
                $this->morphToRelations[$modelClass][$relation->getMorphType()] = $methodName;
            }
        }
    }

    /**
     * Get the BelongsTo relations for a model.
     *
     * The return value is an array where the keys are the foreign key names and the values are the relation method
     * names. MorphTo relations are also considered BelongsTo relations. The relations are cached for performance
     * reasons.
     *
     * @return array<string, string>
     */
    public function getBelongsToRelationsForModel(string $modelClass): array
    {
        if (array_key_exists($modelClass, $this->belongsToRelations)) {
            return $this->belongsToRelations[$modelClass];
        }

        $this->buildRelationsForModel($modelClass);

        return $this->belongsToRelations[$modelClass];
    }

    /**
     * Get the BelongsTo relation for a model and key.
     *
     * The return value is the relation method name or null if the key is not a BelongsTo relation.
     */
    public function getBelongsToRelationForModelAndKey(string $modelClass, string $key): ?string
    {
        $belongsToRelations = $this->getBelongsToRelationsForModel($modelClass);

        return $belongsToRelations[$key] ?? null;
    }

    /**
     * Get the MorphTo relations for a model.
     *
     * The return value is an array where the keys are the morph type column names and the values are the relation
     * method names. The relations are cached for performance reasons.
     *
     * @return array<string, string>
     */
    public function getMorphToRelationsForModel(string $modelClass): array
    {
        if (array_key_exists($modelClass, $this->morphToRelations)) {
            return $this->morphToRelations[$modelClass];
        }

        $this->buildRelationsForModel($modelClass);

        return $this->morphToRelations[$modelClass];
    }

    /**
     * Check if a key is the type property of a MorphTo relation for a model.
     */
    public function isMorphTypeKey(string $modelClass, string $key): bool
    {
        $morphToRelations = $this->getMorphToRelationsForModel($modelClass);

        return array_key_exists($key, $morphToRelations);
    }
}
