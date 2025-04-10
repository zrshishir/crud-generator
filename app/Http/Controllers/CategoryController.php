<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function store(CategoryRequest $request)
    {
        $model = Category::create($request->validated());
        return response()->json($model, 201);
    }

    public function show(Category $model)
    {
        return response()->json($model);
    }

    public function update(CategoryRequest $request, Category $model)
    {
        $model->update($request->validated());
        return response()->json($model);
    }

    public function destroy(Category $model)
    {
        $model->delete();
        return response()->json(null, 204);
    }
}