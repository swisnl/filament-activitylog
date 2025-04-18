<?php

namespace Swis\Filament\Activitylog\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Stringable;
use Swis\Filament\Activitylog\AttributeTable\Contracts\HasValue;

/**
 * @property int $id
 * @property string $name
 */
class ModelWithValue extends Model implements HasValue
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Swis\Filament\Activitylog\Tests\Database\Factories\ModelWithValueFactory> */
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
