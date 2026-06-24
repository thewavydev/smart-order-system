<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Jobs\SendOrderEmailJob;
use App\Models\OrderItem;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = Order::with([
                'customer:id,phone_number',
                'items.product:id,name'
            ])
            ->select('id', 'customer_id', 'total', 'source', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $orders->getCollection()->transform(function ($order) {
            $order->products = $order->items->map(function ($item) {
                return $item->product->name . ' x' . $item->quantity;
            })->implode(', ');

            return $order;
        });

        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'order_number'    => 'required|unique:orders',
            'product_name'    => 'required|string',
            'quantity'        => 'required|integer',
            'total_price'     => 'required|numeric',
            'customer_name'   => 'required|string',
            'customer_email'  => 'required|email',
            'customer_phone'  => 'required|string',
            'customer_address'=> 'required|string',
            // 'status'          => 'required|in:1,2,3',
        ]);

        $order = Order::create($validated);
        SendOrderEmailJob::dispatch($order)->onQueue('emails');

        return response()->json([
            'order' => $order,
            'message' => 'Order Successfully Created'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
