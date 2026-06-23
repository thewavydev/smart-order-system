<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Total Orders
        $totalOrders = Order::count();

        // Total Revenue (assuming orders have total column)
        $totalRevenue = Order::sum('total');

        // Pending Orders
        $pendingOrders = Order::where('status', 'pending')->count();

        // Completed Orders
        $completedOrders = Order::where('status', 'completed')->count();

        //  Most Bought Items
        $mostBoughtItems = OrderItem::select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        // Recent Orders
        $recentOrders = Order::with('items.product')
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'pending_orders' => $pendingOrders,
                'completed_orders' => $completedOrders,
            ],
            'most_bought_items' => $mostBoughtItems,
            'recent_orders' => $recentOrders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
