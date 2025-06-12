<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    // All brands Layout
    public function brands()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    // Add New Brand
    public function add_brand()
    {
        return view('admin.add-brand');
    }
    // Store New Brand
    public function brand_store(Request $request)
    {



        // $request->validate([
        //     'name' => 'required',
        //     'slug' => 'required|unique:brands,slug',
        //     'image' => 'mimes:png,jpg,jpeg|max:2048'
        // ]);


        // $newImageName =  time() . '-' . $request->slug . '.'
        //     . $request->image->extension();


        // $brand = Brand::create([
        //     'name' => $request->input('name'),
        //     'slug' => $request->input('slug'),
        //     'image' => $newImageName,
        // ]);
        // $request->image->move(public_path('uploads/brands'), $newImageName);

        // return redirect('admin/brands')->with('status','Brand has been added successfully', );

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->slug);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;
        $this->GenerateBrandThumbnailImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand has been added sussfuly');
    }

    public function edit_brand($id)
    {
        $brand = Brand::find($id);
        return view('admin.edit-brand', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->slug);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
                File::delete(public_path('uploads/brands') . '/' . $brand->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateBrandThumbnailImage($image, $file_name);
            $brand->image = $file_name;
        }

        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Brand has been Updated successfully');
    }

    public function GenerateBrandThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    // Delete Brand

    public function delete_brand($id)
    {
        $brand = Brand::find($id);
        if (File::exists(public_path('uploads/brands') . '/' . $brand->image)) {
            File::delete(public_path('uploads/brands') . '/' . $brand->image);
        }
        $brand->delete();
        return redirect('admin/brands')->with('status', 'Brand has been deleted successfully',);
    }

    // CATEGORIES
    // LIST
    public function categories()
    {
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    // ADD
    public function add_category()
    {
        return view('admin.new-category');
    }
    // STORE
    public function store_category(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);


        $newImageName =  time() . '-' . $request->slug . '.'
            . $request->image->extension();


        $category = Category::create([
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
            'image' => $newImageName,
        ]);
        $request->image->move(public_path('uploads/categories'), $newImageName);

        return redirect('admin/categories')->with('status', 'Category has been added successfully',);
    }
    // Edit Category
    public function edit_category($id)
    {
        // $car = Car::find($id)->first();

        $category = Category::find($id);
        return view('admin.edit-category')->with('category', $category);
    }

    // UPDATE Category
    public function update_category(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $request->id,
        ]);

        if (!empty($request->file('image'))) {
            $request->validate([
                'image' => 'mimes:png,jpg,jpeg|max:2048'
            ]);
            $test = $request->file('image')->getClientOriginalName();
            $test = str_replace(array('.', 'jpeg', 'PNG', 'png', 'jpg'), '', $test);

            $newImageName =  'updated_' . $test . '.'
                . $request->image->extension();
        }


        $category = Category::where('id', $request->id)
            ->update([
                'name' => $request->input('name'),
                'slug' => $request->input('slug'),
                // 'image' => $newImageName,
            ]);
        $findcategory = Category::find($request->id);

        if (!empty($newImageName)) {
            $category = Category::where('id', $request->id)
                ->update([

                    'image' => $newImageName

                ]);
            if (File::exists(public_path('uploads/categories') . '/' . $findcategory->image)) {
                File::delete(public_path('uploads/categories') . '/' . $findcategory->image);
            }
            $request->image->move(public_path('uploads/categories'), $newImageName);
        }
        return redirect('admin/categories')->with('status', 'Category has been updated successfully',);
    }
    // Delete Category

    public function delete_category($id)
    {
        $category = Category::find($id);
        if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
            File::delete(public_path('uploads/categories') . '/' . $category->image);
        }
        $category->delete();
        return redirect('admin/categories')->with('status', 'Category has been deleted successfully',);
    }

    // All Products Controller
    public function products()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products', compact('products'));
    }
    // Add Product
    public function add_product()
    {
        // Fetch All Categories
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        // Fetch All Brands
        $brands = Brand::select('id', 'name')->orderBy('name')->get();

        return view('admin.add-product', compact('categories', 'brands'));
    }
    // Store Product
    public function store_product(Request $request)
    {
        // Validate Input
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'description' => 'required',
            'short_description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required'
        ]);

        // Product Object
        // Assign Values for the DB
        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->desc = $request->description;
        $product->short_desc = $request->short_description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        // Generate TimeStamp to name the Product Image and Gallery

        $current_timestamp = Carbon::now()->timestamp;
        // Check if Single Product image is Added then Validate and Rename
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp.'.'.$image->extension();

            // Call the GenerateProductThumbnailImage Path Funtion fpr the ImagePath
            $this->GenerateProductThumbnailImage($image, $imageName);
            // Assign Image Values for the DB
            $product->image = $imageName;
        }

        // Generate Array for Product Gallery
        $gallery_arr = array();
        // Set Gallery names to Empty
        $gallery_images = "";
        // Start Counter for the Gallery Array
        $counter = 1;

        if ($request->hasFile('images')) {
            $allowedFileExtession = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextenssion = $file->getClientOriginalExtension();
                $gcheck = in_array($gextenssion, $allowedFileExtession);
                if ($gcheck) {
                    $gfileName = $current_timestamp.'_'.$counter.'.'.$gextenssion;
                    // Call GenerateProductThumbnailImage function for the Gallery Thumbnails Path
                    $this->GenerateProductThumbnailImage($file, $gfileName);

                    // Push each Gallery Image to the Array defined Above
                    array_push($gallery_arr, $gfileName);
                    // Increment the Image Counter as they are been Pushed
                    $counter = $counter + 1;
                }
            }
            // If all is passed, Get all images in array separates by Comma
            $gallery_images = implode(',', $gallery_arr);
        }
        // The Assign GAllery Images Values for the DB
        $product->images = $gallery_images;
        // dd($product->images);
        // And Save records to DB
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Products has been added successfully');


        // $file_extension = $request->file('image')->extension();
        // $file_name = Carbon::now()->timestamp . '.' . $file_extension;
        // $this->GenerateBrandThumbnailImage($image, $file_name);
        // $product->image = $file_name;

    }
    public function GenerateProductThumbnailImage($image, $imageName)
    {
        $destinationPathThumbnails = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->path());

        $img->cover(540, 689, "top");
        $img->resize(540, 689, function($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);

        $img->resize(104, 104, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPathThumbnails . '/' . $imageName);
    }

//    Edit Product
    public function edit_product($id)
    {
        $product = Product::find($id);
         $categories = Category::select('id', 'name')->orderBy('name')->get();
            // Fetch All Brands
            $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.edit-product', compact('product', 'categories', 'brands'));

    }


}
