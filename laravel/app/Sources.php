<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sources extends Model
{

    public $timestamps = false;

    public function scrapesLog()
    {
        return $this->hasMany('App\ScrapesLog', 'source_id');
    }

    public function authors()
    {
        return $this->belongsToMany('App\Authors', 'sources_authors', 'source_id', 'author_id')
            ->withPivot('original_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Categories', 'sources_categories', 'source_id', 'category_id')
            ->withPivot('original_id');
    }
}
