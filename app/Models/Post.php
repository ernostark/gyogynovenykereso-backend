<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'author_id',
        'category_id',
        'published_at',
        'status',
    ];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function author()
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    /**
     * Kapcsolat a kategóriával.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Állapot ellenőrzés: publikált-e a bejegyzés.
     */
    public function isPublished()
    {
        return $this->status === 'published';
    }

    /**
     * Slug automatikus beállítása (opcionális, ha nem küldesz slugot a frontendről).
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = \Illuminate\Support\Str::slug($post->title);
            }
        });
    }
}
