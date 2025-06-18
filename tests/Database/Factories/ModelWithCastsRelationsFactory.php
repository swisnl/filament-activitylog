<?php

namespace Swis\Filament\ActivityLog\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Swis\Filament\ActivityLog\Tests\Models\ModelWithCastsRelations;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Swis\Filament\ActivityLog\Tests\Models\ModelWithCastsRelations>
 */
class ModelWithCastsRelationsFactory extends Factory
{
    protected $model = ModelWithCastsRelations::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
