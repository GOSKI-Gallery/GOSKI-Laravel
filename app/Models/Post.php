<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{

    use HasFactory;

    protected $fillable = [
        'description',
        'image_url',
        'is_nsfw',
        'moderation_status',
        'user_id',
    ];

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): belongsToMany
    {
        return $this->belongsToMany(Like::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)
                    ->withPivot('confidence')
                    ->withTimestamps();
    }
    
}
