<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Product;
use App\Models\Advertise;
use App\Models\Post;

class SearchController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if($request->has('search_key') && $request->search_key != null) {
            $advertises = Advertise::where([
              ['start_date', '<=', date('Y-m-d')],
              ['end_date', '>=', date('Y-m-d')],
              ['at_home_page', '=', false]
            ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

            $products = Product::select('id','name', 'image', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate')
            ->where('name', 'LIKE', '%' . $request->search_key . '%')
            ->whereHas('product_detail', function (Builder $query) {
                $query->where('quantity', '>', 0);
            })
            ->with(['product_detail' => function($query) {
              $query->select('id', 'product_id', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->where('quantity', '>', 0)->orderBy('sale_price', 'ASC');
            }])->latest()->limit(19)->get();

            $posts = Post::select('id', 'title', 'image', 'created_at')
            ->where('title', 'LIKE', '%' . $request->search_key . '%')->get();

            return view('pages.search')->with(['data' => ['advertises' => $advertises, 'posts' => $posts, 'products' => $products]]);
        } else {
            return redirect()->route('home_page');
        }
    }
}
