<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'author_id',
        'category_id',
        'published_at',
        'status',
        'image_path',
        'diseases',
        'featured',
    ];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'diseases' => 'array',
        'featured' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
                $count = static::where('slug', $post->slug)->count();
                if ($count > 0) {
                    $post->slug .= '-' . ($count + 1);
                }
            }
        });
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : asset('storage/images/default.png');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
