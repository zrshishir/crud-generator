<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Requests\ArticleRequest;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        return Article::all();
    }

    public function store(ArticleRequest $request)
    {
        $model = Article::create($request->validated());
        return response()->json($model, 201);
    }

    public function show(Article $model)
    {
        return response()->json($model);
    }

    public function update(ArticleRequest $request, Article $model)
    {
        $model->update($request->validated());
        return response()->json($model);
    }

    public function destroy(Article $model)
    {
        $model->delete();
        return response()->json(null, 204);
    }
}