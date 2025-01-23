<?php

namespace App\Http\Controllers;

use App\Models\Herb;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = auth()->user()->orders;
        return response()->json($orders);
    }

    public function store(Request $request)
{
    $user = $request->user();
    $order = $user->orders()->create([
        'total_price' => 0,
    ]);

    return response()->json(['order' => $order], 201);
}

    public function show($id)
    {
        $order = Order::with('orderItems.herb')->find($id);

        if (!$order || $order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }
}
