<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    public $timestamps = false;

    protected $hidden = ['pivot'];

    protected $fillable = [
        'name', 'slug', 'description'
    ];

    public function articles()
    {
        return $this->belongsToMany('App\Articles', 'articles_categories', 'category_id', 'article_id');
    }

    public function sources()
    {
        return $this->belongsToMany('App\Sources', 'sources_categories', 'category_id', 'source_id')
            ->withPivot('original_id');
    }
}
