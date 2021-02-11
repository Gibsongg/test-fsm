<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

class JobFactory extends Factory
{
    protected string $model = Job::class;

    #[ArrayShape(['name' => "string", 'description' => "string", 'status' => "int[]"])]
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'status' => ['new' => 1],
        ];
    }
}
