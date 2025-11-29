<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleReminder extends Model
{
    protected $fillable = [
        'schedule_id',
        'minutes_before',
        'remind_at',
        'is_sent',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'remind_at' => 'datetime',
            'sent_at' => 'datetime',
            'is_sent' => 'boolean',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(\Zap\Models\Schedule::class);
    }
}
