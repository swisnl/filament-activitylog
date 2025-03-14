<?php

namespace Swis\Filament\Activitylog\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Swis\Filament\Activitylog\Tests\Models\ModelWithCastsRelations;

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
