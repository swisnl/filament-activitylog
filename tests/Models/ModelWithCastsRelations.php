<?php

namespace Swis\Filament\Activitylog\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Stringable;
use Swis\Filament\Activitylog\AttributeTableBuilder;
use Swis\Filament\Activitylog\Contracts\AttributeTableValuesFormatter;
use Swis\Filament\Activitylog\Contracts\SkipsAttributeTableAttributes;

/**
 * @property int $id
 * @property string $name
 */
class ModelWithCastsRelations extends Model implements AttributeTableValuesFormatter, SkipsAttributeTableAttributes
{
    use HasFactory;

    protected $table = 'models_with_casts_relations';

    protected $fillable = [
        'name',
    ];

    public function getCasts()
    {
        return [
            'date_field' => 'date',
            'datetime_field' => 'datetime',
        ];
    }

    public function formatAttributeTableValue(AttributeTableBuilder $builder, mixed $value, string $key, array $attributes, string $recordClass): Stringable | string | null
    {
        if ($key === 'property_with_model_override') {
            return 'model_override';
        }

        return null;
    }

    public function modelWithLabel(): BelongsTo
    {
        return $this->belongsTo(ModelWithLabel::class);
    }

    public function customBelongsToRelation(): BelongsTo
    {
        return $this->belongsTo(ModelWithLabel::class, 'unexpected_foreign_key');
    }

    public function morphedModel(): MorphTo
    {
        return $this->morphTo();
    }

    public function customMorphToRelation(): MorphTo
    {
        return $this->morphTo('morph_to_name', 'unexpected_morph_to_type_field', 'unexpected_morph_to_id_field');
    }

    public function skipAttributeTableAttributes(): array
    {
        return ['property_to_skip'];
    }
}
