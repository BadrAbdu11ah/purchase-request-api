<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function getProducts()
    {
        $products = Product::with('category')->get();

        return response()->json(['status' => 'success', 'data' => $products]);
    }

    public function index(Request $request)
    {
        // إذا كان الأدمن يطلب البيانات، اعرض الكل، وإذا كان يوزر، اعرض طلباته فقط
        $user = $request->user();
        $query = Order::with('details.product')->latest();

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $orders = $query->paginate(10); // يفضل استخدام الترقيم للأداء العالي
        return response()->json(['status' => 'success', 'data' => $orders]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.1',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $order = Order::create([
                    'status' => 'pending',
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($validated['items'] as $item) {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'تم إرسال الطلب بنجاح',
                    'data' => $order->load('details.product'),
                ], 201);
            });
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $order = Order::with('details.product')->find($id);

        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'الطلب غير موجود'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $order]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,purchased,delivered',
        ]);

        $order = Order::find($id);
        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'الطلب غير موجود'], 404);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث حالة الطلب إلى '.$request->status,
            'data' => $order,
        ]);
    }
}
