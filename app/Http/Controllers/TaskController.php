<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\TaskRequest;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return Task::all();
    }

    public function store(TaskRequest $request)
    {
        $model = Task::create($request->validated());
        return response()->json($model, 201);
    }

    public function show(Task $model)
    {
        return response()->json($model);
    }

    public function update(TaskRequest $request, Task $model)
    {
        $model->update($request->validated());
        return response()->json($model);
    }

    public function destroy(Task $model)
    {
        $model->delete();
        return response()->json(null, 204);
    }
}