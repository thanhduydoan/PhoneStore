<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use App\User;
use App\Models\Advertise;

class UserController extends Controller
{
  public function show()
  {
    if(Auth::check()) {
      if (Auth::user()->admin) {
        return redirect()->route('admin.dashboard')->with(['alert' => [
          'type' => 'warning',
          'title' => 'Cảnh Báo',
          'content' => 'Bạn không có quyền truy cập vào trang này!'
        ]]);
      } else {
        $advertises = Advertise::where([
          ['start_date', '<=', date('Y-m-d')],
          ['end_date', '>=', date('Y-m-d')],
          ['at_home_page', '=', false]
        ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

        $user = User::select('id', 'name', 'email', 'phone', 'address', 'avatar_image')
          ->where('id', Auth::user()->id)->first();

        return view('pages.show_user')->with('data',['user' => $user, 'advertises' => $advertises]);
      }
    } else {
      return redirect()->route('login')->with(['alert' => [
        'type' => 'warning',
        'title' => 'Cảnh Báo',
        'content' => 'Bạn phải đăng nhập để sử dụng chức năng này!'
      ]]);
    }
  }

  public function edit()
  {
    if(Auth::check()) {
      if (Auth::user()->admin) {
        return redirect()->route('admin.dashboard')->with(['alert' => [
          'type' => 'warning',
          'title' => 'Cảnh Báo',
          'content' => 'Bạn không có quyền truy cập vào trang này!'
        ]]);
      } else {
        $advertises = Advertise::where([
          ['start_date', '<=', date('Y-m-d')],
          ['end_date', '>=', date('Y-m-d')],
          ['at_home_page', '=', false]
        ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

        $user = User::select('id', 'name', 'email', 'phone', 'address', 'avatar_image')
          ->where('id', Auth::user()->id)->first();

        return view('pages.edit_user')->with('data',['user' => $user, 'advertises' => $advertises]);
      }
    } else {
      return redirect()->route('login')->with(['alert' => [
        'type' => 'warning',
        'title' => 'Cảnh Báo',
        'content' => 'Bạn phải đăng nhập để sử dụng chức năng này!'
      ]]);
    }
  }

  public function save(Request $request)
  {
    if(Auth::check()) {
      if (Auth::user()->admin) {
        return redirect()->route('admin.dashboard')->with(['alert' => [
          'type' => 'warning',
          'title' => 'Cảnh Báo',
          'content' => 'Bạn không có quyền thực hiện chức năng này!'
        ]]);
      } elseif(Auth::user()->id != $request->user_id) {
        return back()->with(['alert' => [
          'type' => 'info',
          'title' => 'Thông Báo',
          'content' => 'Đã xẩy ra lỗi trong quá trình cập nhật thông tin. Vui lòng nhập lại!'
        ]]);
      } else {
        if($request->phone != Auth::user()->phone) {
          $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:20',
            'phone' => 'required|string|size:10|regex:/^0[^6421][0-9]{8}$/|unique:users',
            'address' => 'required',
          ], [
            'name.required' => 'Tên không được để trống!',
            'name.string' => 'Tên phải là một chuỗi ký tự!',
            'name.max' => 'Tên không được vượt quá :max kí tự!',
            'phone.required' => 'Số điện thoại không được để trống!',
            'phone.string' => 'Số điện thoại phải là một chuỗi ký tự!',
            'phone.size' => 'Số điện thoại phải có độ dài :size chữ số!',
            'phone.regex' => 'Số điện thoại không hợp lệ!',
            'phone.unique' => 'Số điện thoại đã tồn tại!',
            'address.required' => 'Địa chỉ không được để trống!',
          ]);
        } else {
          $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:20',
            'address' => 'required',
          ], [
            'name.required' => 'Tên không được để trống!',
            'name.string' => 'Tên phải là một chuỗi ký tự!',
            'name.max' => 'Tên không được vượt quá :max kí tự!',
            'address.required' => 'Địa chỉ không được để trống!',
          ]);
        }

        if ($validator->fails()) {
          return back()
            ->withErrors($validator)
            ->withInput();
        }

        $user = User::where('id', $request->user_id)->first();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;

        if($request->hasFile('avatar_image')){
          $image = $request->file('avatar_image');
          $image_name = time().'_'.$image->getClientOriginalName();
          $image->storeAs('images/avatars',$image_name,'public');

          if($user->avatar_image != NULL) {
            Storage::disk('public')->delete('images/avatars/'.$user->avatar_image);
          }

          $user->avatar_image = $image_name;
        }

        $user->save();

        return redirect()->route('show_user')->with(['alert' => [
          'type' => 'success',
          'title' => 'Thành Công',
          'content' => 'Cập nhật thông tin tài khoản thành công.'
        ]]);
      }
    } else {
      return redirect()->route('login')->with(['alert' => [
        'type' => 'warning',
        'title' => 'Cảnh Báo',
        'content' => 'Bạn phải đăng nhập để sử dụng chức năng này!'
      ]]);
    }
  }
}
