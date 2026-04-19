<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $ordersCount = Order::count();
        $productsCount = Product::count();
        $categoriesCount = Category::count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_orders'     => $ordersCount,
                'total_products'   => $productsCount,
                'total_categories' => $categoriesCount,
            ]
        ]);
    }
}
