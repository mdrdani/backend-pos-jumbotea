<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    //
    public function index(Request $request)
    {
        //get data products
        $products = DB::table('products')
            ->when($request->input('name'), function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        //sort by created_at desc

        return view('pages.product.index', compact('products'));
    }

    public function create()
    {
        return view('pages.product.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:products|min:3',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'category' => 'required|in:Food,Drink,Snack',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $filename = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/products', $filename);
        $data = $request->all();

        $product = new Product;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->category = $request->category;
        $product->image = $filename;
        $product->description = $request->description;
        $product->save();
        return redirect()->route('product.index')->with('success', 'Product successfully created');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('pages.product.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $product = Product::findOrFail($id);
        $product->update($data);
        return redirect()->route('product.index')->with('success', 'Product successfully updated');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('product.index')->with('success', 'Product successfully deleted');
    }
}
