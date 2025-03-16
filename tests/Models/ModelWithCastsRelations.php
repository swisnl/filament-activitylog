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
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Swis\Filament\Activitylog\Tests\Database\Factories\ModelWithCastsRelationsFactory> */
    use HasFactory;

    protected $table = 'models_with_casts_relations';

    protected $fillable = [
        'name',
    ];

    /**
     * @return array<string, string>
     */
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Swis\Filament\Activitylog\Tests\Models\ModelWithLabel, $this>
     */
    public function modelWithLabel(): BelongsTo
    {
        return $this->belongsTo(ModelWithLabel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Swis\Filament\Activitylog\Tests\Models\ModelWithLabel, $this>
     */
    public function customBelongsToRelation(): BelongsTo
    {
        return $this->belongsTo(ModelWithLabel::class, 'unexpected_foreign_key');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model, $this>
     */
    public function morphedModel(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model, $this>
     */
    public function customMorphToRelation(): MorphTo
    {
        return $this->morphTo('morph_to_name', 'unexpected_morph_to_type_field', 'unexpected_morph_to_id_field');
    }

    public function skipAttributeTableAttributes(): array
    {
        return ['property_to_skip'];
    }
}
