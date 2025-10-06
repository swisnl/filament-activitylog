<?php

namespace Swis\Filament\Activitylog\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Swis\Filament\Activitylog\Tests\Models\ModelWithValue;

/**
 * @extends Factory<ModelWithValue>
 */
class ModelWithValueFactory extends Factory
{
    protected $model = ModelWithValue::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
