<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
   // عرض قائمة الطلبات حسب الصلاحيات
    public function index(Request $request)
    {
        $user = $request->user();

        // تحميل العلاقات لتقليل استعلامات قاعدة البيانات N+1
        $query = Order::with(['user', 'details.product'])->latest();

        // منطق الصلاحيات: الأدمن ومسؤول المشتريات يرى كل شيء، الموظف يرى طلباته فقط
        if (!in_array($user->role, ['admin', 'purchasing_officer'])) {
            $query->where('user_id', $user->id);
        }

        // استخدام الباجينيشن لسرعة الأداء في التطبيق
        $orders = $query->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
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
            return DB::transaction(function () use ($validated, $request) {
                
                $productIds = collect($validated['items'])->pluck('product_id');
                $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'status' => 'pending',
                    'notes' => $validated['notes'] ?? null,
                    'total_estimated_price' => 0, 
                ]);

                $totalPrice = 0;
                $orderDetails = [];

                foreach ($validated['items'] as $item) {
                    $product = $products[$item['product_id']];
                    $itemPrice = $product->price * $item['quantity'];
                    $totalPrice += $itemPrice;

                    $orderDetails[] = [
                        'order_id'   => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity'   => $item['quantity'],
                        'actual_price' => null, 
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                OrderDetail::insert($orderDetails);
                $order->update(['total_estimated_price' => $totalPrice]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'تم إرسال الطلب بنجاح', // القيمة الإجمالية ستحسب بالريال
                    'data' => $order->load('details.product'),
                ], 201);
            });

        } catch (\Exception $e) {
            Log::error("Error creating order: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء معالجة الطلب، يرجى المحاولة لاحقاً',
            ], 500);
        }
    }

    // عرض تفاصيل طلب محدد
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $query = Order::with(['user', 'details.product'])->where('id', $id);

        // حماية البيانات: لا يمكن لغير صاحب الطلب أو المسؤول رؤيته
        if (!in_array($user->role, ['admin', 'purchasing_officer'])) {
            $query->where('user_id', $user->id);
        }

        $order = $query->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'الطلب غير موجود أو غير مصرح لك بعرضه'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

    // تحديث حالة الطلب (خاص بالمسؤولين فقط)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,purchased,delivered',
        ]);

        $user = $request->user();

        // التحقق من الصلاحية (فقط Admin أو Purchasing Officer)
        if (!in_array($user->role, ['admin', 'purchasing_officer'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح لك بتغيير حالة الطلب'
            ], 403);
        }

        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'الطلب غير موجود'
            ], 404);
        }

        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث حالة الطلب إلى ' . $request->status,
            'data' => $order
        ]);
    }
    // حذف الطلب (مع التحقق من الصلاحيات والحالة)
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::find($id);

        // 1. التحقق من وجود الطلب
        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'الطلب غير موجود'
            ], 404);
        }

        // 2. التحقق من الصلاحيات: صاحب الطلب أو المسؤولين فقط
        if (!in_array($user->role, ['admin', 'purchasing_officer']) && $order->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح لك بحذف هذا الطلب'
            ], 403);
        }

        // 3. منع الحذف إذا كان الطلب قيد المعالجة أو تم تسليمه
        // في أنظمة المشتريات، الحذف مسموح فقط لحالة الانتظار (التي لم يتم البدء في معالجتها)
        if ($order->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يمكن حذف طلب تم البدء في معالجته أو تسليمه'
            ], 422);
        }

        try {
            return DB::transaction(function () use ($order) {
                // حذف تفاصيل الطلب أولاً (في حال لم تستخدم cascade في قاعدة البيانات)
                $order->details()->delete();
                
                // حذف الطلب الأساسي
                $order->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'تم حذف الطلب بنجاح'
                ], 200);
            });
        } catch (\Exception $e) {
            Log::error("Error deleting order ID {$id}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء محاولة الحذف، يرجى المحاولة لاحقاً'
            ], 500);
        }
    }
}