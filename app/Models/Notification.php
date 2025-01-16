<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

class Notification extends Model
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


    protected $table = 'notifications';

    protected $fillable = [
        'uuid',
        'user_id',
        'receiver_id',
        'post_id',
        'subscriber_unique_id',
        'edu_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
    ];

    // Each notification belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function posts()
    {
        return $this->hasMany(Post::class, 'post_id', 'post_id');
    }

    public function educationals()
    {
        return $this->belongsTo(Educational::class, 'edu_id', 'edu_id');
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsUnRead()
    {
        $this->update([
            'is_read' => false,
        ]);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
