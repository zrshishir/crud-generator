<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(ProductRequest $request)
    {
        $model = Product::create($request->validated());
        return response()->json($model, 201);
    }

    public function show(Product $model)
    {
        return response()->json($model);
    }

    public function update(ProductRequest $request, Product $model)
    {
        $model->update($request->validated());
        return response()->json($model);
    }

    public function destroy(Product $model)
    {
        $model->delete();
        return response()->json(null, 204);
    }
}