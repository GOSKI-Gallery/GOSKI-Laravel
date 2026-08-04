<?php

namespace App\Models;

use App\Models\Concerns\HasSchemaPrefix;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $moderation_status
 */
class Post extends Model
{
    use HasFactory, HasSchemaPrefix;

    protected $fillable = [
        'description',
        'image_url',
        'latitude',
        'longitude',
        'location_name',
        'is_nsfw',
        'moderation_status',
        'user_id',
    ];

    protected $casts = [
        'is_nsfw' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, self::qualifyTable('post_tag'))
            ->withPivot('confidence')
            ->withTimestamps();
    }
}
