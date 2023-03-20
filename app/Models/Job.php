<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'employee_type',
        'employee_id',
        'date',
        'total_hours',
    ];

    /**
     * @var string[]
     */
    protected array $enumEmployeeTypes = [
        'Trader',
        'Professor',
    ];

    /**
     * @return MorphTo
     */
    public function employee(): MorphTo
    {
        return $this->morphTo();
    }
}
