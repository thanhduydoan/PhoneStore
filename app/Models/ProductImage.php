<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
  public function product_detail() {
    return $this->belongsTo('App\Models\ProductDetail');
  }
}
