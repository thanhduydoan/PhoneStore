<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

/**
 * param vote rate
 * return string html start vote
 */
class Helpers
{
  public static function get_start_vote($rate) {
    $output = '';
    if($rate == 0){
      $output = '<i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate > 0 && $rate < 1) {
      $output = '<i class="fas fa-star-half-alt"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate == 1) {
      $output = '<i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate > 1 && $rate < 2) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate == 2) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate > 2 && $rate < 3) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate == 3) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate > 3 && $rate < 4) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i><i class="far fa-star"></i>';
    } elseif ($rate == 4) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>';
    } elseif ($rate > 4 && $rate < 5) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>';
    } elseif ($rate == 5) {
      $output = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>';
    }
    return $output;
  }
  public static function get_promotion_percent($price, $promotion_price, $promotion_start_date, $promotion_end_date) {
    $output = '';
    if($promotion_price != null && $promotion_start_date <= date('Y-m-d') && $promotion_end_date >= date('Y-m-d')) {
      $output = '<div class="promotion-percent">Giảm ' . round(100 * ($price - $promotion_price) / $price) . '%</div>';
    }
    return $output;
  }
  public static function get_real_price($price, $promotion_price, $promotion_start_date, $promotion_end_date) {
    $output = '';
    if($promotion_price != null && $promotion_start_date <= date('Y-m-d') && $promotion_end_date >= date('Y-m-d'))
      $output = '<strong>'.number_format($promotion_price,0,',','.').'₫</strong><del>'.number_format($price,0,',','.').'₫</del>';
    else
      $output = '<strong>'.number_format($price,0,',','.').'₫</strong>';
    return $output;
  }
  public static function get_image_avatar_url($image = null) {
    if($image != null)
      return asset('storage/images/avatars/'.$image);
    else
      return asset('images/no_avatar.jpg');
  }
  public static function get_image_product_url($image = null) {
    if($image != null)
      return asset('storage/images/products/'.$image);
    else
      return asset('images/no_image.png');
  }
  public static function get_image_advertise_url($image = null) {
    if($image != null)
      return asset('storage/images/advertises/'.$image);
    else
      return asset('images/no_image.png');
  }
  public static function get_image_post_url($image = null) {
    if($image != null)
      return asset('storage/images/posts/'.$image);
    else
      return asset('images/no_image.png');
  }

  public static function check_active($array_route) {
    $name_route = request()->route()->getName();
    return in_array($name_route, $array_route) ? 'active' : null;
  }
  public static function check_param_active($route, $id) {
    $name_route = request()->route()->getName();
    $param = request()->route()->parameter('id');
    return ($name_route == $route) && ($param == $id) ? 'active' : null;
  }
}
