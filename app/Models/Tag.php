<?php

namespace App\Models;

use App\Models\Concerns\HasSchemaPrefix;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory, HasSchemaPrefix;

    protected $fillable = ['name'];

    public function posts()
    {
        return $this->belongsToMany(Post::class, self::qualifyTable('post_tag'))
            ->withPivot('confidence')
            ->withTimestamps();
    }
}
