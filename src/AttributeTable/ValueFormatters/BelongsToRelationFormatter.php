<?php

namespace Swis\Filament\ActivityLog\AttributeTable\ValueFormatters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Stringable;
use Swis\Filament\ActivityLog\AttributeTable\Builder;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\ModelRelationFinder;
use Swis\Filament\ActivityLog\AttributeTable\Contracts\ValueFormatter;

class BelongsToRelationFormatter implements ValueFormatter
{
    protected ModelRelationFinder $modelRelationFinder;

    public function __construct(ModelRelationFinder $modelRelationFinder)
    {
        $this->modelRelationFinder = $modelRelationFinder;
    }

    public function formatAttributeTableValue(Builder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null
    {
        if (empty($value) || ! is_scalar($value)) {
            return null;
        }

        $relationName = $this->modelRelationFinder->getBelongsToRelationForModelAndKey($recordClass, $key);
        if (! isset($relationName)) {
            return null;
        }

        $relation = (new $recordClass)->$relationName();

        if ($relation instanceof MorphTo) {
            $type_key = $relation->getMorphType();
            if (empty($attributes[$type_key]) || ! is_string($attributes[$type_key])) {
                return null;
            }

            $relatedModel = Model::getActualClassNameForMorph($attributes[$type_key]);
            if (! $relatedModel || ! is_a($relatedModel, Model::class, true)) {
                return null;
            }

            $valueModel = $relatedModel::find($value);
            if ($valueModel) {
                return $builder->formatValue($valueModel, $key, $attributes, $recordClass);
            } else {
                $alias = $attributes[$type_key];

                return $alias . ': ' . (string) $value;
            }
        }

        /** @var Model $relatedModel */
        $relatedModel = $relation->getRelated();

        $valueModel = $relatedModel->newQuery()->find($value);
        if ($valueModel) {
            return $builder->formatValue($valueModel, $key, $attributes, $recordClass);
        } else {
            $class = get_class($relatedModel);
            $alias = array_search($class, Relation::$morphMap, strict: true) ?: $class;

            if ($alias === $class) {
                $alias = class_basename($class);
            }

            return $alias . ': ' . (string) $value;
        }
    }
}
