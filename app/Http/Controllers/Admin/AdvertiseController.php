<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use App\Models\Advertise;

class AdvertiseController extends Controller
{
  public function index()
  {
    $advertises = Advertise::select('id', 'title', 'image', 'at_home_page', 'start_date', 'end_date', 'created_at')->latest()->get();
    return view('admin.advertise.index')->with('advertises', $advertises);
  }
  public function new()
  {
    return view('admin.advertise.new');
  }
  public function save(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'title' => 'required',
      'at_home_page' => 'required',
      'date' => 'required',
      'image' => 'required',
    ], [
      'title.required' => 'Tiêu đề quảng cáo không được để trống!',
      'at_home_page.required' => 'Vị trí trang hiển thị phải được chọn!',
      'date.required' => 'Ngày bắt đầu và kết thúc phải được chọn!',
      'image.required' => 'Hình ảnh hiển thị bài viết phải được tải lên!',
    ]);

    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    //Xử lý ngày bắt đầu, ngày kết thúc
    list($start_date, $end_date) = explode(' - ', $request->date);

    $start_date = str_replace('/', '-', $start_date);
    $start_date = date('Y-m-d', strtotime($start_date));

    $end_date = str_replace('/', '-', $end_date);
    $end_date = date('Y-m-d', strtotime($end_date));

    $advertise = new Advertise;
    $advertise->title = $request->title;

    if($request->hasFile('image')){
      $image = $request->file('image');
      $image_name = time().'_'.$image->getClientOriginalName();
      $image->storeAs('images/advertises',$image_name,'public');
      $advertise->image = $image_name;
    }

    $advertise->at_home_page = $request->at_home_page;
    $advertise->start_date = $start_date;
    $advertise->end_date = $end_date;

    $advertise->save();

    return redirect()->route('admin.advertise.index')->with(['alert' => [
      'type' => 'success',
      'title' => 'Thành Công',
      'content' => 'Quảng cáo của bạn đã được tạo thành công.'
    ]]);
  }

  public function delete(Request $request)
  {
    $advertise = Advertise::where('id', $request->advertise_id)->first();

    if(!$advertise) {

      $data['type'] = 'error';
      $data['title'] = 'Thất Bại';
      $data['content'] = 'Bạn không thể xóa quảng cáo không tồn tại!';
    } else {
      Storage::disk('public')->delete('images/advertises/' . $advertise->image);

      $advertise->delete();

      $data['type'] = 'success';
      $data['title'] = 'Thành Công';
      $data['content'] = 'Xóa quảng cáo thành công!';
    }

    return response()->json($data, 200);
  }

  public function edit($id)
  {
    $advertise = Advertise::where('id', $id)->first();
    if(!$advertise) abort(404);
    return view('admin.advertise.edit')->with('advertise', $advertise);
  }

  public function update(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'title' => 'required',
      'at_home_page' => 'required',
      'date' => 'required',
    ], [
      'title.required' => 'Tiêu đề quảng cáo không được để trống!',
      'at_home_page.required' => 'Vị trí trang hiển thị phải được chọn!',
      'date.required' => 'Ngày bắt đầu và kết thúc phải được chọn!',
    ]);

    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    //Xử lý ngày bắt đầu, ngày kết thúc
    list($start_date, $end_date) = explode(' - ', $request->date);

    $start_date = str_replace('/', '-', $start_date);
    $start_date = date('Y-m-d', strtotime($start_date));

    $end_date = str_replace('/', '-', $end_date);
    $end_date = date('Y-m-d', strtotime($end_date));

    $advertise = Advertise::where('id', $id)->first();
    $advertise->title = $request->title;

    if($request->hasFile('image')){
      $image = $request->file('image');
      $image_name = time().'_'.$image->getClientOriginalName();
      $image->storeAs('images/advertises',$image_name,'public');
      Storage::disk('public')->delete('images/advertises/' . $advertise->image);
      $advertise->image = $image_name;
    }

    $advertise->at_home_page = $request->at_home_page;
    $advertise->start_date = $start_date;
    $advertise->end_date = $end_date;

    $advertise->save();

    return redirect()->route('admin.advertise.index')->with(['alert' => [
      'type' => 'success',
      'title' => 'Thành Công',
      'content' => 'Quảng cáo của bạn đã được cập nhật thành công.'
    ]]);
  }
}
