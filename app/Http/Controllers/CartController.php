<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    // Add To Cart
    public function add_to_cart(Request $request)
    {
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity,$request->price)->associate('App\Models\Product');
        return redirect()->back();
    }

    // INcrease Cart Quatity
    public function increase_cart_qty($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }
    // Decrease Cart Quatity
    public function decrease_cart_qty($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }
    // REMOVE CART ITEM
    public function remove_cart_item($rowId)
    {
         Cart::instance('cart')->remove($rowId);
        return redirect()->back();

    }
    // Clear CART
    public function empty_cart()
    {
         Cart::instance('cart')->destroy();
        return redirect()->back();

    }

}
