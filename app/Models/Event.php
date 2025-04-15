<?php

namespace App\Models;

use App\Models\Enums\EventEnum;
use App\Models\Enums\EventStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'event_type',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user', 'event_id', 'user_id');
    }

    public function isPast(): bool
    {
        return $this->end_time < now();
    }

    public function isUpcoming(): bool
    {
        return $this->start_time > now();
    }

    public function isOngoing(): bool
    {
        return $this->start_time <= now() && $this->end_time >= now();
    }

    public function getStatusLabelAttribute(): string
    {
        return EventStatusEnum::from($this->status)->label();
    }

    public function getTypeLabelAttribute(): string
    {
        return EventEnum::from($this->event_type)->label();
    }
}
