<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

use App\User;
use App\Models\Post;
use App\Models\Product;
use App\Models\Order;

class DashboardController extends Controller
{
  public function index() {

    $count['user'] = User::where([['active', true], ['admin', false]])->count();
    $count['post'] = Post::count();
    $count['product'] = Product::whereHas('product_details', function (Builder $query) {
      $query->where('quantity', '>', 0);
    })->count();
    $count['order'] = Order::where('status', true)->count();
    return view('admin.index')->with(['count' => $count]);
  }
}
