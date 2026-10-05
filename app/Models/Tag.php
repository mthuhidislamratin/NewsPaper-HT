<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'seo_title', 'meta_description', 'canonical_url', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public static function booted(): void
    {
        static::creating(function (self $tag) {
            $tag->slug ??= Str::slug($tag->name);
        });

        static::saving(function (self $tag) {
            $tag->slug ??= Str::slug($tag->name);
        });
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $key = strtolower($this->slug ?: $this->name);

        return config('jago24.tag_names.'.$key, $this->name);
    }
}
