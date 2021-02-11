<?php

namespace Database\Seeders;

use App\Models\Claim;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Task::factory(1)->create();
        Task::factory(1)->create([
            'name' => 'Задача (устарела)',
            'created_at' => '2021-01-01 00:00:00'
        ]);
    }
}
