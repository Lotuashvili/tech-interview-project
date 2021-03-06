<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Achievement[] $achievements
 * @property-read int|null $achievements_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read string $badge
 * @property-read Collection $next_available_achievements
 * @property-read string|null $next_badge
 * @property-read int|null $next_badge_key
 * @property-read int $remaining_to_unlock_next_badge
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Lesson[] $lessons
 * @property-read int|null $lessons_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Lesson[] $watched
 * @property-read int|null $watched_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The comments that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function watched(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    /**
     * Achievements relation with custom Pivot model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class)->using(AchievementUser::class)->withTimestamps();
    }

    /**
     * Return current badge name depending on achievements count
     *
     * @return string
     */
    public function getBadgeAttribute(): string
    {
        $achievements = $this->achievements()->count();
        $badges = config('settings.badges');

        if (array_key_exists($achievements, $badges)) {
            return $badges[$achievements];
        }

        $key = collect($badges)->keys()->filter(fn(int $count) => $count <= $achievements)->sort()->last() ?: 0;

        return $badges[$key];
    }

    /**
     * Return achievements count for the next badge
     * Returns null if user already has the highest badge
     *
     * @return int|null
     */
    public function getNextBadgeKeyAttribute(): ?int
    {
        $achievements = $this->achievements()->count();

        return collect(config('settings.badges'))->keys()->filter(fn(int $count) => $count > $achievements)->sort()->first();
    }

    /**
     * Return next available badge or null
     *
     * @return string|null
     */
    public function getNextBadgeAttribute(): ?string
    {
        $key = $this->next_badge_key;

        if (is_null($key)) {
            return null;
        }

        return config('settings.badges.' . $key);
    }

    /**
     * Return count of remaining achievements to the next badge
     *
     * @return int
     */
    public function getRemainingToUnlockNextBadgeAttribute(): int
    {
        $key = $this->next_badge_key;

        if (is_null($key)) {
            return 0;
        }

        return max($key - $this->achievements()->count(), 0);
    }

    /**
     * Return next available achievements
     * Grouped by "achievable_type"
     *
     * Using MySQL 8 Window Functions
     *
     * @return \Illuminate\Support\Collection
     */
    public function getNextAvailableAchievementsAttribute(): Collection
    {
        // Using window functions from MySQL 8
        // Partition achievements by type and select only first record from group
        $query = Achievement::select([
            'id',
            'name',
            'count',
            'achievable_type',
            DB::raw('ROW_NUMBER() OVER(PARTITION BY achievable_type ORDER BY count ASC) AS row_num'),
        ])->whereDoesntHave('users', fn($query) => $query->where('id', $this->id));

        return DB::table($query)
            ->select(['id', 'name', 'count', 'achievable_type'])
            ->where('row_num', 1) // Filter first rows in groups
            ->get();
    }
}
