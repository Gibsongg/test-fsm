<?php

namespace Database\Factories;

use App\Dictionary\TaskStatusDictionary;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

class TaskFactory extends Factory
{
    protected string $model = Task::class;

    #[ArrayShape(['name' => "string", 'description' => "string", 'status' => "string"])] public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'status' => TaskStatusDictionary::OPEN,
        ];
    }
}
