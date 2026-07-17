<?php

namespace App\Models;

use App\Models\Concerns\HasSchemaPrefix;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $username
 * @property string $role
 * @property string|null $profile_photo_url
 */
class User extends Authenticatable
{
    use HasFactory, HasSchemaPrefix, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'role',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, self::qualifyTable('likes'), 'user_id', 'post_id');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, self::qualifyTable('follows'), 'followed_id', 'follower_id');
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, self::qualifyTable('follows'), 'follower_id', 'followed_id');
    }
}
