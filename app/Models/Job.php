<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ZeroDaHero\LaravelWorkflow\Traits\WorkflowTrait;

/**
 * Class Job
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property array $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Job extends Model
{
    use HasFactory;
    use WorkflowTrait;

    protected $fillable = ['name', 'description', 'status'];

    protected $casts = [
        'status' => 'array',
        'created_at' => 'datetime:d.m.Y'
    ];

}
