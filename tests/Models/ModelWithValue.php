<?php

namespace Swis\Filament\Activitylog\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Contracts\HasValue;
use Swis\Filament\Activitylog\Tests\Database\Factories\ModelWithValueFactory;

/**
 * @property int $id
 * @property string $name
 */
class ModelWithValue extends Model implements HasValue
{
    /** @use HasFactory<ModelWithValueFactory> */
    use HasFactory;

    protected $table = 'models_with_value';

    protected $fillable = [
        'name',
    ];

    public function getAttributeTableValue(): string | Stringable
    {
        return new HtmlString('<strong>' . e($this->name) . '</strong>');
    }
}
