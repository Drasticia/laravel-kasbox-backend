<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request){
        $products = DB::table('products')
        ->when($request->input('name'), function ($query, $name){
            return $query->where('name', 'like', '%'.$name.'%');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        return view('pages.products.index', compact('products'));
    }

    public function create(){
        return view('pages.products.create');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|min:3|unique:products',
            'description' => 'nullable|min:10',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'category' => 'required|in:food,drink,snack',
            'img' => 'required|image|mimes:png,jpg,jpeg',
        ]);
        
    
        $filename = time() . '.' . $request->img->extension();
        $request->img->storeAs('public/products', $filename);
        $data = $request->all();

        $product = new \App\Models\Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = (int) $request->price;
        $product->stock = (int) $request->stock;
        $product->category = $request->category;
        $product->img = $filename;
        $product->save();

        return redirect()->route('product.index')->with('success', 'Product successfully created');
    }
    

    public function edit($id){
        $product = \App\Models\Product::findOrFail($id);
        return view('pages.products.edit', compact('product'));
    }

    public function update(Request $request, $id){
        $data = $request->all();
        $product = \App\Models\Product::findOrFail($id);
        $product->update($data);
        return redirect()->route('product.index')->with('success', 'Product successfully updated');
    }

    public function destroy($id){
        $product = \App\Models\Product::findOrFail($id);
        $product->delete();
        return redirect()->route('product.index')->with('success', 'Product successfully deleted');
    }
}
