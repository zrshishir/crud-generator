<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Http\Requests\TagRequest;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }

    public function store(TagRequest $request)
    {
        $model = Tag::create($request->validated());
        return response()->json($model, 201);
    }

    public function show(Tag $model)
    {
        return response()->json($model);
    }

    public function update(TagRequest $request, Tag $model)
    {
        $model->update($request->validated());
        return response()->json($model);
    }

    public function destroy(Tag $model)
    {
        $model->delete();
        return response()->json(null, 204);
    }
}