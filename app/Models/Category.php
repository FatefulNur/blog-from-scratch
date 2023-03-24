<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function posts()
    {
        return $this->hasMany(Blog::class);
    }

    public function getRouteKeyName()
    {
        return "name";
    }

    /**
     * Count all the childrens and descendants of a category
     *
     * @return integer
     */
    public function countDescendants()
    {
        $sum = 0;

        if ($this->children->count()) {
            $sum += $this->children->count();
            foreach ($this->children as $child) {
                if ($child->children->count()) {
                    $sum += $child->countDescendants();
                }
            }
        }

        return $sum;
    }

    /**
     * Create nested category route of this category
     *
     * @return string
     */
    public function ancestorsToRoute()
    {
        return implode("/", array_reverse($this->ancestors()));
    }

    /**
     * Get ancestors of the category instance
     *
     * @return array
     */
    private function ancestors()
    {
        $ancestors = [];
        array_push($ancestors, $this->name);

        if ($this->parent_id) {
            $parent = Category::find($this->parent_id);
            array_push($ancestors, $parent->name);
            array_pop($ancestors);
            array_push($ancestors, ...$parent->ancestors());
        }

        return $ancestors;
    }
}
