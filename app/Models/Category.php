<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
    ];

    /**
     * Kapcsolat a gyermek kategóriákkal.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Kapcsolat a szülő kategóriával.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Kapcsolat a kategóriához tartozó posztokkal.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Automatikus slug generálás.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });
    }
}
