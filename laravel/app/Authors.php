<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Authors extends Model
{
    public $timestamps = false;

    public function articles()
    {
        return $this->belongsToMany('App\Articles', 'articles_authors', 'author_id', 'article_id');
    }
}
