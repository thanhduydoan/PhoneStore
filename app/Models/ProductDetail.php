<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
  public function product() {
    return $this->belongsTo('App\Models\Product');
  }
  public function product_images() {
    return $this->hasMany('App\Models\ProductImage');
  }
  public function order_details() {
    return $this->hasMany('App\Models\OrderDetail');
  }
}
