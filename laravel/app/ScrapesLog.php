<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScrapesLog extends Model
{

    public $timestamps = false;

    protected $table = 'scrapes_log';


    public function source()
    {
        return $this->belongsTo('App\Sources', 'source_id');
    }
}
