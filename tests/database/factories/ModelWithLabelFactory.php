<?php

namespace Swis\Filament\Activitylog\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Swis\Filament\Activitylog\Tests\Models\ModelWithLabel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Swis\Filament\Activitylog\Tests\Models\ModelWithLabel>
 */
class ModelWithLabelFactory extends Factory
{
    protected $model = ModelWithLabel::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
