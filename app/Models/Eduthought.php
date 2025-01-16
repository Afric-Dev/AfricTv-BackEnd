<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;


class Eduthought extends Model
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

    protected $table = "eduthoughts";

    protected $fillable = [
        "edu_id",
        "user_id",
        "parent_id",
        "thoughts",
        "thoughts_vid_path",
        "thoughts_img_path",
        "thoughts_link",
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
        return $this->belongsTo(Eduthought::class, 'parent_id');
    }
    // Get child Eduthought (replies)
    public function replies()
    {
        return $this->hasMany(Eduthought::class, 'parent_id')->with('replies'); // Recursive relationship for nested thought
    }
}
