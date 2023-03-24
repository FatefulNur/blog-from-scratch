<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

     /**
     *  Get the blog image
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'imagable')->withDefault();
    }

    /**
     *  Get the blog user
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     *  Get the blog gallery
     */
    public function gallery()
    {
        return $this->hasOne(Gallery::class)->withDefault([
            'user_id' => null,
            'blog_id' => null
        ]);
    }

    /** ======================<><blogs helper><>============================ */
    /** ======================<><blogs helper><>============================ */
    /** ======================<><blogs helper><>============================ */
    /** ======================<><blogs helper><>============================ */

    /**
     * get the post excerpt
     */
    public function excerpt($length = 15, $end = '...')
    {
        return Str::limit($this->description, $length, $end);
    }

    public function defaultThumbnail()
    {
        if (is_null($this->image->path)) {
            return $this->image->path = "uploads/default.svg";
        }

        return $this->image->path;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault([
            'name' => 'Uncategorized'
        ]);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
