<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaLibrary extends Model
{
    protected $table = 'media_library';

    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'alt_text',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'uploaded_by' => 'integer',
    ];

    public function scopeImages($query)
    {
        return $query->where('file_type', 'image');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
