<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  public function advertises() {
    return $this->hasMany('App\Models\Advertise');
  }
  public function comments() {
    return $this->hasMany('App\Models\Comment');
  }
  public function product_votes() {
    return $this->hasMany('App\Models\ProductVote');
  }
  public function promotions() {
    return $this->hasMany('App\Models\Promotion');
  }
  public function product_details() {
    return $this->hasMany('App\Models\ProductDetail');
  }
  public function producer() {
    return $this->belongsTo('App\Models\Producer');
  }
  public function product_detail() {
    return $this->hasOne('App\Models\ProductDetail', 'product_id', 'id');
  }
}
