<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'site_id',
        'parent_id',
        'parameter',
        'title',
        'description',
        'keywords',
        'filters',
        'robots',
    ];

    /**
     * Alt elemanlar
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->belongsTo(Category::class, 'id', 'parent_id');
    }

    /**
     * Üst Elemanlar
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Tüm Üst elemanları getirir
     *
     * @return \Illuminate\Support\Collection
     */
    public function getParentsAttribute()
    {
        $parents = collect([]);

        $parent = $this->parent;

        while (!is_null($parent)) {
            $parents->push($parent);
            $parent = $parent->parent;
        }

        return $parents;
    }

    public function getChildrensAttribute()
    {
        $childrens = collect([]);

        $children = $this->children;

        while (!is_null($children)) {
            $childrens->push($children);
            $children = $children->children;
        }

        return $childrens;
    }
}
