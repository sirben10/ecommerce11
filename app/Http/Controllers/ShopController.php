<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size') ? $request->query('size') : 12;
        $o_column = "";
        $o_order = "";
        $order = $request->query('order') ? $request->query('order') : -1;
        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');
        switch ($order) {
            case 1:
                $o_column = 'created_at';
                $o_order = 'DESC';
                break;
            case 2:
                $o_column = 'created_at';
                $o_order = 'ASC';
                break;
            case 3:
                $o_column = 'regular_price';
                $o_order = 'ASC';
                break;
            case 4:
                $o_column = 'regular_price';
                $o_order = 'DESC';
                break;

            default:
                $o_column = 'id';
                $o_order = 'DESC';
                break;
        }
        // $products = Product::orderBy($o_column, $o_order)->paginate($size);
        $products = Product::where(function($query)use($f_brands){
            $query->whereIn('brand_id', explode(',',$f_brands))->orWhereRaw("'".$f_brands."'=''");
        })->where(function($query)use($f_categories){
            $query->whereIn('category_id', explode(',',$f_categories))->orWhereRaw("'".$f_categories."'=''");
        })->orderBy($o_column, $o_order)->paginate($size);
        // $categories = Category::select('id', 'name')->orderBy('name')->get();
        $categories = Category::orderBy('name', 'ASC')->get();
        // Fetch All Brands
        $brands = Brand::orderBy('name', 'ASC')->get();
        return view('shop', compact('products', 'categories', 'brands', 'size', 'order','f_brands','f_categories'));
    }

    // Product detail
    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $rproducts = Product::where('slug', '<>', $product_slug)->get()->take(5);
        return view('details', compact('product', 'rproducts'));
    }
}
