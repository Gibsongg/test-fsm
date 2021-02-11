<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ZeroDaHero\LaravelWorkflow\Traits\WorkflowTrait;

/**
 * Class Task
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $status
 * @property string $estimate
 * @property int $assignee
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Task extends Model
{
    use HasFactory;
    use WorkflowTrait;

    protected $fillable = ['name', 'description', 'status'];

    /**
     * Проверка для guard
     * @param int $days
     * @return bool
     */
    public function isOverdue(int $days): bool
    {
        $date = $this->created_at->addDays($days);
        $currentDate = Carbon::now();

        if ($currentDate <= $date) {
            return true;
        }

        return false;
    }
}
