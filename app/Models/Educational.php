<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class Educational extends Model
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

    protected $table = "education";

    protected $fillable = [
            "edu_id",
            "user_id",
            "title",
            "description",
            "links",
            "edu_vid_path",
            "eduvideoId",
            "edu_views", 
            "vote_count",
            "thoughts_count",
            "favourites_count",
            "is_status"
    ];
    
    public function user()
   {
       return $this->belongsTo(User::class, 'user_id');
   }
}
