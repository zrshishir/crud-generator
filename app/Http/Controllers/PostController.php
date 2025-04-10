<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }

    public function store(PostRequest $request)
    {
        $model = Post::create($request->validated());
        return response()->json($model, 201);
    }

    public function show(Post $model)
    {
        return response()->json($model);
    }

    public function update(PostRequest $request, Post $model)
    {
        $model->update($request->validated());
        return response()->json($model);
    }

    public function destroy(Post $model)
    {
        $model->delete();
        return response()->json(null, 204);
    }
}