<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class Comments extends Model
{
    use HasFactory;

    protected $keyType = 'uuid';

    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            $customer->{$customer->getKeyName()} = (string) Str::uuid();
        });
    }

    protected $fillable = [
        "post_id",
        "user_id",
        "parent_id",
        "comments",
        "comments_vid_path",
        "comments_img_path",
        "comments_link",
    ];
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
       return $this->belongsTo(User::class);
    }
    // Self-referencing relationship for parent comment
    public function parent()
    {
        return $this->belongsTo(Comments::class, 'parent_id');
    }
    // Get child comments (replies)
    public function replies()
    {
        return $this->hasMany(Comments::class, 'parent_id')->with('replies'); // Recursive relationship for nested comments
    }
}
