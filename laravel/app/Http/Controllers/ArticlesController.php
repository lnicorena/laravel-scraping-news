<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use App\Articles;
use App\Categories;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the articles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $articles = Articles::with(['authors', 'categories'])
            ->orderBy('date_pub', 'desc');

        $category = intval($request->input('category'));
        if ($category)
            $articles->whereHas('categories', function (Builder $query) use ($category) {
                $query->where('id', '=', $category);
            });

        return $articles->paginate(10);
    }

    /**
     * Display the list of categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function categories()
    {
        $categories = Categories::select()
            ->withCount(['articles' => function ($query) {
                return $query->groupBy('category_id');
            }])
            ->orderBy('articles_count', 'desc')
            ->limit(10);
            
        return $categories->get();
    }
}
