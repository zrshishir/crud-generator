<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return Order::all();
    }

    public function store(OrderRequest $request)
    {
        $model = Order::create($request->validated());
        return response()->json($model, 201);
    }

    public function show(Order $model)
    {
        return response()->json($model);
    }

    public function update(OrderRequest $request, Order $model)
    {
        $model->update($request->validated());
        return response()->json($model);
    }

    public function destroy(Order $model)
    {
        $model->delete();
        return response()->json(null, 204);
    }
}