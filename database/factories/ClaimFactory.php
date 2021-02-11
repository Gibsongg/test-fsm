<?php

namespace Database\Factories;

use App\Dictionary\ClaimStatusDictionary;
use App\Models\Claim;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

class ClaimFactory extends Factory
{
    protected string $model = Claim::class;

    #[ArrayShape(['name' => "string", 'description' => "string", 'status' => "string"])] public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'status' => ClaimStatusDictionary::NEW,
        ];
    }
}
