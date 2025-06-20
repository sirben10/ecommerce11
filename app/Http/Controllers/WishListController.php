<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class WishListController extends Controller
{
    public function add_to_whichlist(Request $request)
    {
        Cart::instance('wishlist')->add($request->id,$request->name, $request->quantity, $request->price)->associate('App\Models\Model');
        return redirect()->back();
    }
}
