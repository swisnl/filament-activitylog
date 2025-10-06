<?php

namespace Swis\Filament\Activitylog\Tests\Models;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Swis\Filament\Activitylog\Tests\Database\Factories\ModelWithLabelFactory;

/**
 * @property int $id
 * @property string $name
 */
class ModelWithLabel extends Model implements HasLabel
{
    /** @use HasFactory<ModelWithLabelFactory> */
    use HasFactory;

    protected $table = 'models_with_label';

    protected $fillable = [
        'name',
    ];

    public function getLabel(): string
    {
        return $this->name;
    }
}
