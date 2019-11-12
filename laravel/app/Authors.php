<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Authors extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'slug', 'description', 'position', 'avatar', 'twitter', 'linkedin', 'facebook'
    ];

    public function articles()
    {
        return $this->belongsToMany('App\Articles', 'articles_authors', 'author_id', 'article_id');
    }

    public function sources()
    {
        return $this->belongsToMany('App\Sources', 'sources_authors', 'author_id', 'source_id');
    }
}
