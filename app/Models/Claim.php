<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ZeroDaHero\LaravelWorkflow\Traits\WorkflowTrait;

/**
 * Class Claim
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Claim extends Model
{
    use HasFactory;
    use WorkflowTrait;

    protected $fillable = ['name', 'description', 'status'];


    /**
     * Проверка для события guard
     * @param int $days
     * @return bool
     */
    public function isOverdue(int $days): bool
    {
        $date = $this->created_at->addDays($days);
        $currentDate = Carbon::now();

        if ($currentDate > $date) {
            return false;
        }

        return true;
    }

}
