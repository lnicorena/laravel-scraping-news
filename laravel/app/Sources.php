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
}
