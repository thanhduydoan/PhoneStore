<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
  public function order() {
    return $this->belongsTo('App\Models\Order');
  }
  public function product_detail() {
    return $this->belongsTo('App\Models\ProductDetail');
  }
}
