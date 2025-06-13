<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(6);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        // Fetch All Brands
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('shop', compact('products', 'categories', 'brands'));
    }

    // Product detail
    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $rproducts = Product::where('slug', '<>', $product_slug)->get()->take(5);
        return view('details', compact('product', 'rproducts'));
    }
}
