<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Producer;

class HeaderComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $producers;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct()
    {
        // Dependencies automatically resolved by service container...
        $this->producers = Producer::select('id', 'name')->get();
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('producers', $this->producers);
    }
}
