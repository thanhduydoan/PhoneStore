<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Session;
use App\Models\ProductDetail;
use App\Models\Cart;

class CartComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $cart;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct()
    {
        // Dependencies automatically resolved by service container...
        $oldCart = Session::has('cart') ? Session::get('cart') : NULL;
        $cart = new Cart($oldCart);
        $cart->update();
        $this->cart = $cart;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('cart', $this->cart);
    }
}
