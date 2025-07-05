<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Slide;
use App\Models\Transaction;
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
        $brand->slug = Str::slug($request->name);
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
            $imageName = $current_timestamp . '.' . $image->extension();

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
                    $gfileName = $current_timestamp . '_' . $counter . '.' . $gextenssion;
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
        $img->resize(540, 689, function ($constraint) {
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

    public function update_product(Request $request)
    {
        // Validate Input
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $request->id,
            'description' => 'required',
            'short_description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required'
        ]);

        // Product Object
        // Assign Values for the DB
        $product = Product::find($request->id);
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
            if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
                File::delete(public_path('uploads/products') . '/' . $product->image);
            }
            if (File::exists(public_path('uploads/products/thumbnails') . '/' . $product->image)) {
                File::delete(public_path('uploads/products/thumbnails') . '/' . $product->image);
            }
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();

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
            foreach (explode(',', $product->images) as $oldfile) {
                if (File::exists(public_path('uploads/products') . '/' . $oldfile)) {
                    File::delete(public_path('uploads/products') . '/' . $oldfile);
                }
                if (File::exists(public_path('uploads/products/thumbnails') . '/' . $oldfile)) {
                    File::delete(public_path('uploads/products/thumbnails') . '/' . $oldfile);
                }
            }
            $allowedFileExtession = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextenssion = $file->getClientOriginalExtension();
                $gcheck = in_array($gextenssion, $allowedFileExtession);
                if ($gcheck) {
                    $gfileName = $current_timestamp . '_' . $counter . '.' . $gextenssion;
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
            // The Assign GAllery Images Values for the DB
            $product->images = $gallery_images;
        }

        // dd($product->images);
        // And Save records to DB
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Products has been Updated successfully');
    }

    // Delete Product
    public function delete_product($id)
    {
        $product = Product::find($id);
        if (File::exists(public_path('uploads/products') . '/' . $product->image)) {
            File::delete(public_path('uploads/products') . '/' . $product->image);
        }
        if (File::exists(public_path('uploads/products/thumbnails') . '/' . $product->image)) {
            File::delete(public_path('uploads/products/thumbnails') . '/' . $product->image);
        }

        foreach (explode(',', $product->images) as $oldfile) {
            if (File::exists(public_path('uploads/products') . '/' . $oldfile)) {
                File::delete(public_path('uploads/products') . '/' . $oldfile);
            }
            if (File::exists(public_path('uploads/products/thumbnails') . '/' . $oldfile)) {
                File::delete(public_path('uploads/products/thumbnails') . '/' . $oldfile);
            }
        }
        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Products has been Deleted successfully');
    }

    // COUPONS
    public function coupons()
    {
        $coupons = Coupon::orderBy('expiry_date', 'DESC')->paginate(12);
        return view('admin.coupons', compact('coupons'));
    }
    // Add Coupon
    public function add_coupon()
    {
        return view('admin.add-coupon');
    }
    // Add Coupon
    public function store_coupon(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date'
        ]);
        $coupon = new Coupon();
        $coupon->code =  $request->code;
        $coupon->type =  $request->type;
        $coupon->value =  $request->value;
        $coupon->cart_value =  $request->cart_value;
        $coupon->expiry_date =  $request->expiry_date;
        $coupon->save();


        return redirect()->route('admin.coupons')->with('status', 'Coupon has been added successfully!');
    }

    public function edit_coupon($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.edit-coupon', compact('coupon'));
    }

    public function update_coupon(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required | numeric',
            'cart_value' => 'required | numeric',
            'expiry_date' => 'required | date'
        ]);
        $coupon = Coupon::find($request->id);
        $coupon->code =  $request->code;
        $coupon->type =  $request->type;
        $coupon->value =  $request->value;
        $coupon->cart_value =  $request->cart_value;
        $coupon->expiry_date =  $request->expiry_date;
        $coupon->save();

        return redirect()->route('admin.coupons')->with('status', 'Coupon has been updated successfully!');
    }

    public function delete_coupon($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status', 'Coupon has been deleted successfully!');
    }
    // SHOW ORDERS IN ADMIN
    public function show_orders()
    {
        $orders = Order::orderBy('created_at', 'DESC')->paginate(12);

        return view('admin.orders', compact('orders'));
    }

    // Order Details
    public function order_details($order_id)
    {
        $order = Order::find($order_id);
        $orderItems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();
        return view('admin.order-details', compact('order', 'orderItems', 'transaction'));
    }

    // Order Status
    public function update_order_status(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = $request->order_status;
        if ($request->order_status == 'delivered') {
            $order->delivered_date = Carbon::now();
        } elseif ($request->order_status == 'cancelled') {
            $order->cancelled_date = Carbon::now();
        }
        $order->save();

        if ($request->order_status == 'delivered')
        {
            $transaction = Transaction::where('order_id', $request->order_id)->first();
            $transaction->status = 'approved';
            $transaction->save();
        }
        elseif ($request->order_status == 'cancelled')
        {
            $transaction = Transaction::where('order_id', $request->order_id)->first();
            $transaction->status = 'Declined';
            $transaction->save();
        }
        else{
            $transaction = Transaction::where('order_id', $request->order_id)->first();
            $transaction->status = 'Pending';
            $transaction->save();

        }
        return back()->with('status', 'Status changed successfully!');
    }
    // SLIDES


    public function slides()
    {
        $slides = Slide::orderBy('id', 'Desc')->paginate(12);
        return view('admin.slides', compact('slides'));
    }

    // add slide
    public function add_slide()
    {
        return view('admin.add-slide');
    }
    // Store slide
    public function store_slide(Request $request)
    {
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'required | mimes:png,jpg,jpeg | max:2048'
        ]);

        $slide = new Slide;
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

         $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;
        $this->GenerateSlideThumbnailImage($image, $file_name);
        $slide->image = $file_name;
        $slide->save();

        return redirect()->route('admin.slides')->with('status', 'Slides Added Successfully');
    }
      public function GenerateSlideThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/slides');
        $img = Image::read($image->path());
        $img->cover(400, 690, "top");
        $img->resize(400, 690, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    // Edit Slide
    public function edit_slide($id)
    {
        $slide = Slide::find($id);
        return view('admin.edit-slide', compact('slide'));
    }

      // Update slide
    public function update_slide(Request $request)
    {
        $request->validate([
            'tagline' => 'required',
            'title' => 'required',
            'subtitle' => 'required',
            'link' => 'required',
            'status' => 'required',
            'image' => 'mimes:png,jpg,jpeg | max:2048'
        ]);

        $slide = Slide::find($request->id);
        $slide->tagline = $request->tagline;
        $slide->title = $request->title;
        $slide->subtitle = $request->subtitle;
        $slide->link = $request->link;
        $slide->status = $request->status;

        if($request->hasFile('image'))
        {
            if (File::exists(public_path('uploads/slides') . '/' . $slide->image)) {
                File::delete(public_path('uploads/slides') . '/' . $slide->image);
            }
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extension;
        $this->GenerateSlideThumbnailImage($image, $file_name);
        $slide->image = $file_name;
        }
        $slide->save();

        return redirect()->route('admin.slides')->with('status', 'Slides updated Successfully');
    }
}
