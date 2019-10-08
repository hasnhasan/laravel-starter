<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicRoute extends Model
{
    public $table = 'routes';
    public $timestamps = false;
    protected $casts = [
        'filters' => 'array',
    ];
    protected $fillable = [
        'slug',
        'namespace',
        'parameter',
        'title',
        'description',
        'keywords',
        'filters',
        'robots',
    ];

    protected static function boot()
    {
        parent::boot();
        self::saving(function($item) {
            $item->slug = checkSlug($item->slug, $item->id);

            return $item;
        });

    }
}
