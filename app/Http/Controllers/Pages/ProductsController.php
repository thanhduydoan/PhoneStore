<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\ProductDetail;
use App\Models\Producer;
use App\Models\Product;
use App\Models\Advertise;
use App\Models\ProductVote;

class ProductsController extends Controller
{
  public function index(Request $request) {

    if($request->has('type') && $request->input('type') == 'promotion') {
      $query_products = Product::whereHas('product_detail', function (Builder $query) {
        $query->where([
          ['quantity', '>', 0],
          ['promotion_price', '>', 0],
          ['promotion_start_date', '<=', date('Y-m-d')],
          ['promotion_end_date', '>=', date('Y-m-d')]
        ]);
      });
    } else {
      $query_products = Product::whereHas('product_detail', function (Builder $query) {
        $query->where('quantity', '>', 0);
      });
    }

    $query_products->with(['product_detail' => function($query) {
      $query->select('id', 'product_id', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->where('quantity', '>', 0)->orderBy('sale_price', 'ASC');
    }]);

    if($request->has('name') && $request->input('name') != null)
      $query_products->where('name', 'LIKE', '%' . $request->input('name') . '%');

    if($request->has('os') && $request->input('os') != null)
      $query_products->where('OS', 'LIKE', '%' . $request->input('os') . '%');

    if($request->has('price') && $request->input('price') != null) {
      $min_price_query = ProductDetail::select('product_id', DB::raw('min(sale_price) as min_sale_price'))->where('quantity', '>', 0)->groupBy('product_id');

      $query_products->joinSub($min_price_query, 'min_price_query', function ($join) {
        $join->on('products.id', '=', 'min_price_query.product_id');
      })->select('id','name', 'image', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate')->orderBy('min_sale_price', $request->input('price'));
    } else {
      $query_products->select('id','name', 'image', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate')->latest();
    }

    if($request->has('type') && $request->input('type') == 'vote')
      $query_products->orderBy('rate', 'desc');

    $products = $query_products->paginate(15);

    $advertises = Advertise::where([
      ['start_date', '<=', date('Y-m-d')],
      ['end_date', '>=', date('Y-m-d')],
      ['at_home_page', '=', false]
    ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

    $producers = Producer::select('id', 'name')->get();

    return view('pages.products')->with(['data' => ['advertises' => $advertises, 'producers' => $producers, 'products' => $products]]);
  }

  public function getProducer(Request $request, $id) {

    if($request->has('type') && $request->input('type') == 'promotion') {
      $query_products = Product::whereHas('product_detail', function (Builder $query) {
        $query->where([
          ['quantity', '>', 0],
          ['promotion_price', '>', 0],
          ['promotion_start_date', '<=', date('Y-m-d')],
          ['promotion_end_date', '>=', date('Y-m-d')]
        ]);
      });
    } else {
      $query_products = Product::whereHas('product_detail', function (Builder $query) {
        $query->where('quantity', '>', 0);
      });
    }

    $query_products->with(['product_detail' => function($query) {
      $query->select('id', 'product_id', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->where('quantity', '>', 0)->orderBy('sale_price', 'ASC');
    }]);

    if($request->has('name') && $request->input('name') != null)
      $query_products->where('name', 'LIKE', '%' . $request->input('name') . '%');

    if($request->has('os') && $request->input('os') != null)
      $query_products->where('OS', 'LIKE', '%' . $request->input('os') . '%');

    if($request->has('price') && $request->input('price') != null) {
      $min_price_query = ProductDetail::select('product_id', DB::raw('min(sale_price) as min_sale_price'))->where('quantity', '>', 0)->groupBy('product_id');

      $query_products->joinSub($min_price_query, 'min_price_query', function ($join) {
        $join->on('products.id', '=', 'min_price_query.product_id');
      })->select('id','name', 'image', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate')->orderBy('min_sale_price', $request->input('price'));
    } else {
      $query_products->select('id','name', 'image', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate')->latest();
    }

    if($request->has('type') && $request->input('type') == 'vote')
      $query_products->orderBy('rate', 'desc');

    $products = $query_products->where('producer_id', $id)->paginate(15);

    $advertises = Advertise::where([
      ['start_date', '<=', date('Y-m-d')],
      ['end_date', '>=', date('Y-m-d')],
      ['at_home_page', '=', false]
    ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

    $producers = Producer::where('id', '<>', $id)->select('id', 'name')->get();
    $producer = Producer::select('id', 'name')->find($id);

    if(!$producer) abort(404);

    return view('pages.producer')->with(['data' => ['advertises' => $advertises, 'producers' => $producers, 'products' => $products], 'producer' => $producer]);
  }

  public function getProduct(Request $request, $id) {

    $advertises = Advertise::where([
      ['start_date', '<=', date('Y-m-d')],
      ['end_date', '>=', date('Y-m-d')],
      ['at_home_page', '=', false]
    ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

    $product = Product::select('id', 'producer_id', 'name', 'sku_code', 'monitor', 'front_camera', 'rear_camera', 'CPU', 'GPU', 'RAM', 'ROM', 'OS', 'pin', 'rate', 'information_details', 'product_introduction')
      ->whereHas('product_details', function (Builder $query) {
        $query->where('import_quantity', '>', 0);
      })
      ->where('id', $id)->with(['promotions' => function ($query) {
        $query->select('id', 'product_id', 'content')
              ->where([['start_date', '<=', date('Y-m-d')],
                ['end_date', '>=', date('Y-m-d')]])
              ->latest();
        }])->with(['producer' => function ($query) {
          $query->select('id', 'name');
        }])->first();

    if(!$product) abort(404);

    $product_details = ProductDetail::where([['product_id', $id], ['import_quantity', '>', 0]])->with([
      'product_images' => function ($query) {
        $query->select('id', 'product_detail_id', 'image_name');
      }
    ])->select('id', 'color', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->get();

    $suggest_products = Product::select('id','name', 'image', 'rate')
    ->whereHas('product_detail', function (Builder $query) {
        $query->where('quantity', '>', 0);
    })
    ->where([['producer_id', $product->producer_id], ['id', '<>', $id]])
    ->with(['product_detail' => function($query) {
      $query->select('id', 'product_id', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->where('quantity', '>', 0)->orderBy('sale_price', 'ASC');
    }])->latest()->limit(3)->get();

    $product_votes = ProductVote::whereHas('user', function (Builder $query) {
      $query->where([['active', true], ['admin', false]]);
    })->where('product_id', $id)->with(['user' => function($query) {
      $query->select('id', 'name', 'avatar_image');
    }])->latest()->get();

    return view('pages.product')->with(['data' => ['advertises' => $advertises, 'product' => $product, 'product_details' => $product_details, 'suggest_products' => $suggest_products, 'product_votes' => $product_votes]]);
  }

  public function addVote(Request $request) {
    $vote = ProductVote::updateOrCreate(
        ['user_id' => $request->user_id, 'product_id' => $request->product_id],
        ['content' => $request->content, 'rate' => $request->rate]
    );
    $rate = ProductVote::where('product_id', $request->product_id)->avg('rate');

    $product = Product::where('id', $request->product_id)->first();
    $product->rate = $rate;
    $product->save();

    return back()->with(['vote_alert' => [
        'type' => 'success',
        'title' => 'Đã Gửi Đánh Giá',
        'content' => 'Cảm ơn bạn đã đóng góp về sản phẩm này. Chúng tôi luôn luôn trân trong những đóng góp của bạn.'
    ]]);
  }
}
