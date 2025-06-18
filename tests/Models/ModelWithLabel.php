<?php

namespace Swis\Filament\ActivityLog\Tests\Models;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 */
class ModelWithLabel extends Model implements HasLabel
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Swis\Filament\ActivityLog\Tests\Database\Factories\ModelWithLabelFactory> */
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
