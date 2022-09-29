<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertise extends Model
{
  public function product() {
    return $this->belongsTo('App\Models\Product');
  }
}
