<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\ProjectRequest;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::all();
    }

    public function store(ProjectRequest $request)
    {
        $model = Project::create($request->validated());
        return response()->json($model, 201);
    }

    public function show(Project $model)
    {
        return response()->json($model);
    }

    public function update(ProjectRequest $request, Project $model)
    {
        $model->update($request->validated());
        return response()->json($model);
    }

    public function destroy(Project $model)
    {
        $model->delete();
        return response()->json(null, 204);
    }
}