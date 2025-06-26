<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');
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

    public function apply_coupon_code(Request $request)
    {
        // $coupon = Coupon::get('cart_value')->first();
        // dd(Cart::instance('cart')->subtotal().' - '.$coupon);
        $coupon_code = $request->coupon_code;
        if (isset($coupon_code)) {
            // dd($coupon_code);
            $coupon = Coupon::where('code', $coupon_code)
                ->where('expiry_date', '>=', Carbon::today())
                ->where('cart_value', '<=',  str_replace(',', '', Cart::instance('cart')->subtotal()))->first();
            //    dd(Coupon::get('cart_value').' AND Cart Subtotal is '.  str_replace( ',', '', Cart::instance('cart')->subtotal()));
            if ($coupon) {
                Session::put('coupon', [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value
                ]);
                // dd(Session::get('coupon')['value']);
                $this->calculate_discount();
                return redirect()->back()->with('success', 'Coupon has been applied');
            } else {
                return redirect()->back()->with('error', 'Invalid Coupon Code or Expired');
            }
        } else {
            return redirect()->back()->with('error', 'Coupon Code Not Set');
        }
    }

    public function calculate_discount()
    {
        $discount = 0;
        // if (Session::has('coupon')) {
        //     if (Session::get('coupon')['type']=='fixed') {
        //         $discount = Session::get('coupon')['value'];
        //     }
        //     else {
        //         $total = str_replace( ',', '', Cart::instance('cart')->total());
        //         $discount = $total * (Session::get('coupon')['value']/100);

        //     }
        //     $subtotal = str_replace( ',', '', Cart::instance('cart')->subtotal());
        //     $taxAfterDiscount = ($total * config('cart.tax'))/100;
        //     $totalAfterDiscount = $total - $taxAfterDiscount;
        //     Session::put('discounts', [
        //         'discount' => number_format(floatval(value: $discount),2,'.',''),
        //         'subtotal' => number_format(floatval($subtotal),2,'.',''),
        //         'tax' => number_format(floatval($taxAfterDiscount),2,'.',''),
        //         'total' => number_format(floatval($totalAfterDiscount),2,'.','')
        //     ]);
        // }
        if (Session::has('coupon')) {
            if (Session::get('coupon')['type'] == 'fixed') {
                $discount = Session::get('coupon')['value'];
            } else {
                $discount = str_replace(',', '', Cart::instance('cart')->subtotal()) * (Session::get('coupon')['value'] / 100);
            }
            $subtotalAfterDiscount = str_replace(',', '', Cart::instance('cart')->subtotal()) - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;
            Session::put('discounts', [
                'discount' => number_format(floatval(value: $discount), 2, '.', ''),
                'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '.', ''),
                'tax' => number_format(floatval($taxAfterDiscount), 2, '.', ''),
                'total' => number_format(floatval($totalAfterDiscount), 2, '.', '')
            ]);
        }
    }
    public function remove_coupon_code()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return back()->with('success', 'Coupon has been removed');
    }

    public function checkout()
    {
        if(!Auth::check())
        {
            return redirect()->route('login');
        }

        $address = Address::where('user_id', Auth::user()->id)->where('isdefault', 1)->first();
        return view('checkout', compact('address'));
    }
}
