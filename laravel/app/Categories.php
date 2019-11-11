<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    public $timestamps = false;

    public function articles()
    {
        return $this->belongsToMany('App\Articles', 'articles_categories', 'category_id', 'article_id');
    }
}
