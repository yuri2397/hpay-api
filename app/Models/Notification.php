<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Notification extends Model
{
    use HasFactory, HasUuid;

    const NEW_PASSWORD_NOTIFICATION = 'new_password_notification';
    const NEW_EMAIL_NOTIFICATION = 'new_email_notification';
    const NEW_LOGIN_NOTIFICATION = 'new_login_notification';
    protected $fillable = [
        'notifiable_id',
        'notifiable_type',
        'type',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the parent notifiable model (client, invoice, etc.).
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread()
    {
        if (!is_null($this->read_at)) {
            $this->forceFill(['read_at' => null])->save();
        }
    }
}
