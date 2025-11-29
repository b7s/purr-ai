<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Zap\Models\Concerns\HasSchedules;

class Calendar extends Model
{
    use HasFactory, HasSchedules;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];
}
