<?php

namespace App\Models;

use App\Models\ProductDetail;
use Illuminate\Support\Arr;

class Cart
{
  public $items = NULL;
  public $totalQty = 0;
  public $totalPrice = 0;

  public function __construct($oldCart)
  {
    if($oldCart) {
      $this->items = $oldCart->items;
      $this->totalQty = $oldCart->totalQty;
      $this->totalPrice = $oldCart->totalPrice;
    }
  }

  public function add($item, $id, $qty) {

    $this->update();

    if(($item->promotion_price > 0) && ($item->promotion_start_date <= date('Y-m-d')) && ($item->promotion_end_date >= date('Y-m-d')))
      $storedItem = ['qty' => 0, 'price' => $item->promotion_price, 'item' => $item];
    else
      $storedItem = ['qty' => 0, 'price' => $item->sale_price, 'item' => $item];

    if($this->items && array_key_exists($id, $this->items)) {
      if(($this->items[$id]['qty'] + $qty) > $this->items[$id]['item']->quantity)
        return false;
      else
        $storedItem = $this->items[$id];
    }

    $storedItem['qty'] += $qty;

    $this->items[$id] = $storedItem;
    $this->totalQty += $qty;
    $this->totalPrice += $qty * $this->items[$id]['price'];
    return true;
  }

  public function updateItem($id, $qty) {
    $this->update();
    if($qty > $this->items[$id]['item']->quantity)
      return false;
    else {
      $increase = $qty - $this->items[$id]['qty'];
      $this->items[$id]['qty'] = $qty;
      $this->totalPrice = $this->totalPrice + $increase * $this->items[$id]['price'];
      $this->totalQty = $this->totalQty + $increase;
      return true;
    }
  }

  public function update() {
    if($this->totalQty == 0) {
      return false;
    } else {
      $this->totalPrice = 0;
      foreach($this->items as $key => $item) {
        $product = ProductDetail::where('id',$key)->with(['product' => function($query) {
          $query->select('id', 'name', 'image', 'sku_code', 'RAM', 'ROM');
        }])->select('id', 'product_id', 'color', 'quantity', 'sale_price', 'promotion_price', 'promotion_start_date', 'promotion_end_date')->first();
        $this->items[$key]['item'] = $product;
        if(($product->promotion_price > 0) && ($product->promotion_start_date <= date('Y-m-d')) && ($product->promotion_end_date >= date('Y-m-d')))
          $this->items[$key]['price'] = $product->promotion_price;
        else
          $this->items[$key]['price'] = $product->sale_price;
        $this->totalPrice = $this->totalPrice + $this->items[$key]['price'] * $this->items[$key]['qty'];
      }
      return true;
    }
  }

  public function remove($id) {
    if($this->items && array_key_exists($id, $this->items)) {
      $qty = $this->items[$id]['qty'];
      $price = $this->items[$id]['price'];
      $this->items = Arr::except($this->items, $id);
      $this->totalQty = $this->totalQty - $qty;
      $this->totalPrice = $this->totalPrice - $qty * $price;
      $this->update();
      return true;
    } else {
      return false;
    }
  }
}
