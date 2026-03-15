<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NotificationTemplate extends Model
{
    protected $fillable = ['template_key', 'name', 'subject', 'body', 'type', 'status', 'variables'];

    protected $casts = [
        'variables' => 'array',
    ];

    /**
     * Get a template by key.
     */
    public static function getByKey(string $key): ?self
    {
        return Cache::remember("notification_template_{$key}", 3600, function () use ($key) {
            return static::where('template_key', $key)->where('status', 'active')->first();
        });
    }

    /**
     * Render the template with variables.
     */
    public function render(array $variables = []): array
    {
        $subject = $this->subject;
        $body = $this->body;

        foreach ($variables as $key => $value) {
            $placeholder = '{' . $key . '}';
            $subject = str_replace($placeholder, $value, $subject);
            $body = str_replace($placeholder, $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    /**
     * Clear template cache.
     */
    public static function clearCache(?string $key = null): void
    {
        Cache::forget('notification_templates_all');
        
        if ($key) {
            Cache::forget("notification_template_{$key}");
        } else {
            $keys = static::pluck('template_key');
            foreach ($keys as $k) {
                Cache::forget("notification_template_{$k}");
            }
        }
    }

    /**
     * Scope for active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
