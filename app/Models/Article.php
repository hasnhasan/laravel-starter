<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Mediable;
use App\Traits\Routable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use Filterable, Routable, Mediable;
    protected $fillable = [
        'site_id',
        'user_id',
        'module',
        'title',
        'summary',
        'content',
        'extra_data',
        'status',
        'published_date',
        'expiry_date',
    ];

    protected $dates = [
        'published_date',
        'expiry_date',
    ];
    protected $casts = [
        'extra_data' => 'array',
    ];

    public $frontNameSpace = 'ArticleController@detail';
    public $slugColum = 'title';
}
