<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    /**
     * @return HasMany
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class);
    }

    /**
     * @return BelongsTo
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class, 'employee_id');
    }

    /**
     * @return BelongsTo
     */
    public function trader(): BelongsTo
    {
        return $this->belongsTo(Trader::class, 'employee_id');
    }
}
