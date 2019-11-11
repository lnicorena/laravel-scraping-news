<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Articles;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the articles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Articles::with('authors')
            ->with('categories')
            ->paginate();

        // $s = new \App\ScrapesLog;
        // $s->started_at = "now()";
        // $s->finished_at = "now()";
        // $s->articles_analyzed = 15;
        // $s->articles_imported = 13;

        // $source = \App\Sources::find(1);
        // $source->scrapesLog()->save($s);

        // $author = new \App\Authors;
        // $author->name = "Glu";
        // $author->slug = "glu";
        // $author->position = "ha!";
        // $author->description = "yeah yeah";
        // $author->save();

        // $author = \App\Authors::find(1);

        // $source = \App\Sources::find(1);
        // $a = new \App\Articles;
        // $a->original_id = 0;
        // $a->title = "Bla";
        // $a->slug = "bla";
        // $a->link = '';
        // $a->date_pub = 'now()';
        // $a->date_mod = 'now()';
        // $a->content = '<html>Há!</html>';
        // $a->featured = 1;
        // $a->source()->associate($source);
        // $a->save();


        // $a = \App\Articles::find(2);
        // $author = \App\Authors::find(1);
        // $a->authors()->attach($author);
        // $a->authors()->detach($author);

        // $c = new App\Categories;
        // $c->name = 'Ha!';
        // $c->slug = 'ha';
        // $c->save();

        // $c->articles()->attach(($a));


        // $source->scrapesLog()->save($s);
    }
}
