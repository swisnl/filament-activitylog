<?php

namespace Swis\Filament\ActivityLog\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Swis\Filament\ActivityLog\Tests\Models\ModelWithValue;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Swis\Filament\ActivityLog\Tests\Models\ModelWithValue>
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
