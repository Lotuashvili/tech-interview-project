<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\AchievementUser
 *
 * @property int $achievement_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Achievement $achievement
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser whereAchievementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser whereUserId($value)
 * @mixin \Eloquent
 */
class AchievementUser extends Pivot
{
    protected $fillable = [
        'achievement_id',
        'user_id',
    ];

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
