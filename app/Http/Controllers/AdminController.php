<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
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
            $constraint->aspectRation();
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
}
