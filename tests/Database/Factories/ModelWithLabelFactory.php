<?php

namespace Swis\Filament\ActivityLog\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Swis\Filament\ActivityLog\Tests\Models\ModelWithLabel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Swis\Filament\ActivityLog\Tests\Models\ModelWithLabel>
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
