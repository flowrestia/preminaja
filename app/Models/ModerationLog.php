<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModerationLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'actor_id',
        'action',
        'target_type',
        'target_id',
        'reason',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Helper cepat untuk mencatat log moderasi
     */
    public static function log(string $action, Model $target, ?string $reason = null, ?int $actorId = null): self
    {
        return static::create([
            'actor_id' => $actorId ?? auth()->id(),
            'action' => $action,
            'target_type' => get_class($target),
            'target_id' => $target->getKey(),
            'reason' => $reason,
            'created_at' => now(),
        ]);
    }
}
