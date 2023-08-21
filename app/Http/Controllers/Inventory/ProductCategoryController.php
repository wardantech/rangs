<?php

namespace App\Http\Controllers\Inventory;

use Redirect;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\ProductCategory;

class ProductCategoryController extends Controller
{
    public function index()
    {
        try{
            $productCategories=ProductCategory::all();
            return view('inventory.product_category.index', compact('productCategories'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'=>'required|string|unique:product_categories,name,NULL,id,deleted_at,NULL',
            'code'=>'required|string|unique:product_categories,code,NULL,id,deleted_at,NULL',
            'parent' => 'required'
        ]);
        try {
            $productCategory = $request->all();
            ProductCategory::create($productCategory);
            return redirect()->route('inventory.product-category-index')->with('success', __('New Product Category created successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try{
            $productCategory=ProductCategory::find($id);
            return view('Inventory.product_category.edit', compact('productCategory'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|unique:product_categories,name,' . $id,
            'code' => 'required|string|unique:product_categories,code,' . $id,
            'parent' => 'required'
        ]);

        try {
            $productCategory=ProductCategory::find($id);
            $productCategory->name = $request->name;
            $productCategory->code = $request->code;
            $productCategory->parent_id = $request->parent_id;

            $productCategory->save();
            return redirect()->route('product-category-index')->with('success', __('Product category updated successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id){
        try {
            ProductCategory::findOrFail($id)->delete();
            return redirect()->route('inventory.product-category-index')->with('success', __('Product Category deleted successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
}
