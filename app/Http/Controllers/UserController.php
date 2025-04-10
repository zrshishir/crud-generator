<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(UserRequest $request)
    {
        $model = User::create($request->validated());
        return response()->json($model, 201);
    }

    public function show(User $model)
    {
        return response()->json($model);
    }

    public function update(UserRequest $request, User $model)
    {
        $model->update($request->validated());
        return response()->json($model);
    }

    public function destroy(User $model)
    {
        $model->delete();
        return response()->json(null, 204);
    }
}