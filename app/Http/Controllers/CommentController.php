<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        return Comment::all();
    }

    public function store(CommentRequest $request)
    {
        $model = Comment::create($request->validated());
        return response()->json($model, 201);
    }

    public function show(Comment $model)
    {
        return response()->json($model);
    }

    public function update(CommentRequest $request, Comment $model)
    {
        $model->update($request->validated());
        return response()->json($model);
    }

    public function destroy(Comment $model)
    {
        $model->delete();
        return response()->json(null, 204);
    }
}