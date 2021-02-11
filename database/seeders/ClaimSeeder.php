<?php

namespace Database\Seeders;

use App\Models\Claim;
use Illuminate\Database\Seeder;

class ClaimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Claim::factory(1)->create();
        Claim::factory(1)->create([
            'name' => 'Заявка (устарела)',
            'created_at' => '2021-01-01 00:00:00'
        ]);
    }
}
