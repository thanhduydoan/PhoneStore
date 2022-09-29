<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\ProductDetail;
use App\Models\Cart;
use App\Models\Advertise;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\OrderDetail;
use App\NL_Checkout;

class CartController extends Controller
{
  public function addCart(Request $request) {

    $product = ProductDetail::where('id',$request->id)
    ->with(['product' => function($query) {
      $query->select('id', 'name', 'image', 'sku_code', 'RAM', 'ROM');
    }])->select('id', 'product_id', 'color', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->first();

    if(!$product) {
      $data['msg'] = 'Product Not Found!';
      return response()->json($data, 404);
    }

    $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
    $cart = new Cart($oldCart);
    if(!$cart->add($product, $product->id, $request->qty)) {
      $data['msg'] = 'Số lượng sản phẩm trong giỏ vượt quá số lượng sản phẩm trong kho!';
      return response()->json($data, 412);
    }
    Session::put('cart', $cart);

    $data['msg'] = "Thêm giỏ hàng thành công";
    $data['url'] = route('home_page');
    $data['response'] = Session::get('cart');

    return response()->json($data, 200);
  }

  public function removeCart(Request $request) {

    $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
    $cart = new Cart($oldCart);

    if(!$cart->remove($request->id)) {
      $data['msg'] = 'Sản Phẩm không tồn tại!';
      return response()->json($data, 404);
    } else {
      Session::put('cart', $cart);

      $data['msg'] = "Xóa sản phẩm thành công";
      $data['url'] = route('home_page');
      $data['response'] = Session::get('cart');

      return response()->json($data, 200);
    }
  }

  public function updateCart(Request $request) {
    $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
    $cart = new Cart($oldCart);
    if(!$cart->updateItem($request->id, $request->qty)) {
      $data['msg'] = 'Số lượng sản phẩm trong giỏ vượt quá số lượng sản phẩm trong kho!';
      return response()->json($data, 412);
    }
    Session::put('cart', $cart);

    $response = array(
      'id' => $request->id,
      'qty' => $cart->items[$request->id]['qty'],
      'price' => $cart->items[$request->id]['price'],
      'salePrice' => $cart->items[$request->id]['item']->sale_price,
      'totalPrice' => $cart->totalPrice,
      'totalQty' => $cart->totalQty,
      'maxQty'  =>  $cart->items[$request->id]['item']->quantity
    );
    $data['response'] = $response;
    return response()->json($data, 200);
  }

  public function updateMiniCart(Request $request) {
    $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
    $cart = new Cart($oldCart);
    if(!$cart->updateItem($request->id, $request->qty)) {
      $data['msg'] = 'Số lượng sản phẩm trong giỏ vượt quá số lượng sản phẩm trong kho!';
      return response()->json($data, 412);
    }
    Session::put('cart', $cart);

    $response = array(
      'id' => $request->id,
      'qty' => $cart->items[$request->id]['qty'],
      'price' => $cart->items[$request->id]['price'],
      'totalPrice' => $cart->totalPrice,
      'totalQty' => $cart->totalQty,
      'maxQty'  =>  $cart->items[$request->id]['item']->quantity
    );
    $data['response'] = $response;
    return response()->json($data, 200);
  }

  public function showCart() {

    $advertises = Advertise::where([
      ['start_date', '<=', date('Y-m-d')],
      ['end_date', '>=', date('Y-m-d')],
      ['at_home_page', '=', false]
    ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

    $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
    $cart = new Cart($oldCart);

    return view('pages.cart')->with(['cart' => $cart, 'advertises' => $advertises]);
  }

  public function showCheckout(Request $request)
  {
    if(Auth::check() && !Auth::user()->admin) {
      if($request->has('type') && $request->type == 'buy_now') {
        $payment_methods = PaymentMethod::select('id', 'name', 'describe')->get();
        $product = ProductDetail::where('id',$request->id)
          ->with(['product' => function($query) {
            $query->select('id', 'name', 'image', 'sku_code', 'RAM', 'ROM');
          }])->select('id', 'product_id', 'color', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->first();
        $cart = new Cart(NULL);
        if(!$cart->add($product, $product->id, $request->qty)) {
          return back()->with(['alert' => [
              'type' => 'warning',
              'title' => 'Thông Báo',
              'content' => 'Số lượng sản phẩm trong giỏ vượt quá số lượng sản phẩm trong kho!'
          ]]);
        }
        return view('pages.checkout')->with(['cart' => $cart, 'payment_methods' => $payment_methods, 'buy_method' =>$request->type]);
      } elseif($request->has('type') && $request->type == 'buy_cart') {

        $payment_methods = PaymentMethod::select('id', 'name', 'describe')->get();
        $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
        $cart = new Cart($oldCart);
        $cart->update();
        Session::put('cart', $cart);
        return view('pages.checkout')->with(['cart' => $cart, 'payment_methods' => $payment_methods, 'buy_method' =>$request->type]);
      }
    } elseif(Auth::check() && Auth::user()->admin) {
      return redirect()->route('home_page')->with(['alert' => [
        'type' => 'error',
        'title' => 'Thông Báo',
        'content' => 'Bạn không có quyền truy cập vào trang này!'
      ]]);
    } else {
      return redirect()->route('login')->with(['alert' => [
        'type' => 'info',
        'title' => 'Thông Báo',
        'content' => 'Bạn hãy đăng nhập để mua hàng!'
      ]]);
    }
  }

  public function payment(Request $request) {
    $payment_method = PaymentMethod::select('id', 'name')->where('id', $request->payment_method)->first();
    if(Str::contains($payment_method->name, 'COD')) {
      if($request->buy_method == 'buy_now'){
        $order = new Order;
        $order->user_id = Auth::user()->id;
        $order->payment_method_id = $request->payment_method;
        $order->order_code = 'PSO'.str_pad(rand(0, pow(10, 5) - 1), 5, '0', STR_PAD_LEFT);
        $order->name = $request->name;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->status = 1;
        $order->save();

        $order_details = new OrderDetail;
        $order_details->order_id = $order->id;
        $order_details->product_detail_id = $request->product_id;
        $order_details->quantity = $request->totalQty;
        $order_details->price = $request->price;
        $order_details->save();

        $product = ProductDetail::find($request->product_id);
        $product->quantity = $product->quantity - $request->totalQty;
        $product->save();

        return redirect()->route('home_page')->with(['alert' => [
          'type' => 'success',
          'title' => 'Mua hàng thành công',
          'content' => 'Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi. Sản phẩm của bạn sẽ được chuyển đến trong thời gian sớm nhất.'
        ]]);
      } elseif ($request->buy_method == 'buy_cart') {
        $cart = Session::get('cart');

        $order = new Order;
        $order->user_id = Auth::user()->id;
        $order->payment_method_id = $request->payment_method;
        $order->order_code = 'PSO'.str_pad(rand(0, pow(10, 5) - 1), 5, '0', STR_PAD_LEFT);
        $order->name = $request->name;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->status = 1;
        $order->save();

        foreach ($cart->items as $key => $item) {
          $order_details = new OrderDetail;
          $order_details->order_id = $order->id;
          $order_details->product_detail_id = $item['item']->id;
          $order_details->quantity = $item['qty'];
          $order_details->price = $item['price'];
          $order_details->save();

          $product = ProductDetail::find($item['item']->id);
          $product->quantity = $product->quantity - $item['qty'];
          $product->save();
        }
        Session::forget('cart');
        return redirect()->route('home_page')->with(['alert' => [
          'type' => 'success',
          'title' => 'Mua hàng thành công',
          'content' => 'Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi. Sản phẩm của bạn sẽ được chuyển đến trong thời gian sớm nhất.'
        ]]);
      }
    } elseif(Str::contains($payment_method->name, 'Online Payment')) {
      if($request->buy_method == 'buy_now'){
        $order = new Order;
        $order->user_id = Auth::user()->id;
        $order->payment_method_id = $request->payment_method;
        $order->order_code = 'PSO'.str_pad(rand(0, pow(10, 5) - 1), 5, '0', STR_PAD_LEFT);
        $order->name = $request->name;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->status = 0;
        $order->save();

        $order_details = new OrderDetail;
        $order_details->order_id = $order->id;
        $order_details->product_detail_id = $request->product_id;
        $order_details->quantity = $request->totalQty;
        $order_details->price = $request->price;
        $order_details->save();

        // Tạo url thanh toán
        $receiver = env('RECEIVER');
        $order_code = $order->order_code;
        $return_url = route('payment_response');
        $cancel_url = route('payment_response');
        $notify_url = route('payment_response');
        $transaction_info = $order->id;
        $currency = "vnd";
        $quantity = $request->totalQty;
        $price = $order_details->price * $order_details->quantity;
        $tax =0;
        $discount =0;
        $fee_cal =0;
        $fee_shipping =0;
        $order_description ="Thanh toán đơn hàng ".config('app.name');
        $buyer_info = $request->name."*|*".$request->email."*|*".$request->phone."*|*".$request->address;
        $affiliate_code = "";

        $nl= new NL_Checkout();
        $nl->nganluong_url = env('NGANLUONG_URL');
        $nl->merchant_site_code = env('MERCHANT_ID');
        $nl->secure_pass = env('MERCHANT_PASS');

        $url= $nl->buildCheckoutUrlExpand($return_url, $receiver, $transaction_info, $order_code, $price, $currency, $quantity, $tax, $discount , $fee_cal,    $fee_shipping, $order_description, $buyer_info , $affiliate_code);
        $url .='&cancel_url='. $cancel_url . '&notify_url='.$notify_url;

        return redirect()->away($url);
      } elseif ($request->buy_method == 'buy_cart') {
        $cart = Session::get('cart');

        $order = new Order;
        $order->user_id = Auth::user()->id;
        $order->payment_method_id = $request->payment_method;
        $order->order_code = 'PSO'.str_pad(rand(0, pow(10, 5) - 1), 5, '0', STR_PAD_LEFT);
        $order->name = $request->name;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->status = 0;
        $order->save();

        foreach ($cart->items as $key => $item) {
          $order_details = new OrderDetail;
          $order_details->order_id = $order->id;
          $order_details->product_detail_id = $item['item']->id;
          $order_details->quantity = $item['qty'];
          $order_details->price = $item['price'];
          $order_details->save();
        }

        // Tạo url thanh toán
        $receiver = env('RECEIVER');
        $order_code = $order->order_code;
        $return_url = route('payment_response');
        $cancel_url = route('payment_response');
        $notify_url = route('payment_response');
        $transaction_info = $order->id;
        $currency = "vnd";
        $quantity = $cart->totalQty;
        $price = $cart->totalPrice;
        $tax =0;
        $discount =0;
        $fee_cal =0;
        $fee_shipping =0;
        $order_description ="Thanh toán đơn hàng ".config('app.name');
        $buyer_info = $request->name."*|*".$request->email."*|*".$request->phone."*|*".$request->address;
        $affiliate_code = "";

        $nl= new NL_Checkout();
        $nl->nganluong_url = env('NGANLUONG_URL');
        $nl->merchant_site_code = env('MERCHANT_ID');
        $nl->secure_pass = env('MERCHANT_PASS');

        $url= $nl->buildCheckoutUrlExpand($return_url, $receiver, $transaction_info, $order_code, $price, $currency, $quantity, $tax, $discount , $fee_cal,    $fee_shipping, $order_description, $buyer_info , $affiliate_code);
        $url .='&cancel_url='. $cancel_url . '&notify_url='.$notify_url;

        Session::forget('cart');
        return redirect()->away($url);
      }
    }
  }

  public function responsePayment(Request $request) {
    if ($request->filled('payment_id')) {

      // Lấy các tham số để chuyển sang Ngânlượng thanh toán:
      $transaction_info =$request->transaction_info;
      $order_code =$request->order_code;
      $price =$request->price;
      $payment_id =$request->payment_id;
      $payment_type =$request->payment_type;
      $error_text =$request->error_text;
      $secure_code =$request->secure_code;

      //Khai báo đối tượng của lớp NL_Checkout
      $nl= new NL_Checkout();
      $nl->merchant_site_code = env('MERCHANT_ID');
      $nl->secure_pass = env('MERCHANT_PASS');

      //Tạo link thanh toán đến nganluong.vn
      $checkpay= $nl->verifyPaymentUrl($transaction_info, $order_code, $price, $payment_id, $payment_type, $error_text, $secure_code);

      if ($checkpay) {
        $order = Order::where([['id', $transaction_info],['order_code', $order_code]])->first();
        $order->status = 1;
        $order->save();

        foreach ($order->order_details as $order_detail) {
          $product_detail = ProductDetail::where('id', $order_detail->product_detail_id)->first();
          $product_detail->quantity = $product_detail->quantity - $order_detail->quantity;
          $product_detail->save();
        }
        return redirect()->route('home_page')->with(['alert' => [
          'type' => 'success',
          'title' => 'Thanh toán thành công!',
          'content' => 'Cảm ơn bạn đã tin tưởng và lựa chọn chúng tối.'
        ]]);
      }else{
        return redirect()->route('home_page')->with(['alert' => [
          'type' => 'error',
          'title' => 'Thanh toán không thành công!',
          'content' => $error_text
        ]]);
      }
    } else {
      return redirect()->route('home_page')->with(['alert' => [
        'type' => 'error',
        'title' => 'Thanh toán không thành công!',
        'content' => 'Bạn đã hủy hoặc đã xẩy ra lỗi trong quá trình thanh toán.'
      ]]);
    }
  }
}
