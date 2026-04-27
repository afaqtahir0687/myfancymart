<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Utils\CartManager;
use App\Utils\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ResellerController extends Controller
{
    /**
     * Add product to cart for resell
     */
    public function addToCart(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'resell_price' => 'required|numeric|min:0',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = auth('api')->user();
        if (!$customer || $customer == 'offline') {
            $customer = Helpers::getCustomerInformation($request);
        }

        if (!$customer || $customer == 'offline') {
            return response()->json([
                'success' => false,
                'message' => 'Please login to resell'
            ], 401);
        }
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Calculate profit
        $resellProfit = $request->resell_price - $product->unit_price;

        // Add to cart
        $cartData = [
            'product_id' => $request->product_id,
            'customer_id' => $customer->id,
            'quantity' => $request->quantity ?? 1,
            'price' => $request->resell_price,
            'is_resell' => 1,
            'resell_profit' => $resellProfit,
            'resell_commission' => 0,
            'product_type' => $product->product_type,
            'seller_id' => $product->user_id,
            'seller_is' => $product->added_by,
            'slug' => $product->slug,
            'name' => $product->name,
            'thumbnail' => $product->thumbnail,
        ];

        $cart = Cart::create($cartData);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart for resell',
            'data' => [
                'id' => $cart->id,
                'product_id' => $cart->product_id,
                'customer_id' => $cart->customer_id,
                'quantity' => $cart->quantity,
                'price' => $cart->price,
                'is_resell' => $cart->is_resell,
                'resell_profit' => $cart->resell_profit,
                'resell_commission' => $cart->resell_commission,
                'product_type' => $cart->product_type,
                'seller_id' => $cart->seller_id,
                'seller_is' => $cart->seller_is,
                'slug' => $cart->slug,
                'name' => $cart->name,
                'thumbnail' => $cart->thumbnail,
            ]
        ]);
    }

    /**
     * Get cart with resell items
     */
    public function getCart(Request $request): JsonResponse
    {
        $customer = auth('api')->user();
        if (!$customer || $customer == 'offline') {
            $customer = Helpers::getCustomerInformation($request);
        }

        if (!$customer || $customer == 'offline') {
            return response()->json([
                'success' => false,
                'message' => 'Please login'
            ], 401);
        }
        $cartItems = Cart::where('customer_id', $customer->id)
            ->where('is_resell', 1)
            ->with('product')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'is_resell' => $item->is_resell,
                    'resell_profit' => $item->resell_profit,
                    'resell_commission' => $item->resell_commission,
                    'created_at' => $item->created_at,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                        'unit_price' => $item->product->unit_price,
                        'thumbnail' => $item->product->thumbnail,
                        'product_type' => $item->product->product_type,
                    ]
                ];
            })
        ]);
    }

    /**
     * Get order summary with resell calculations
     */
    public function getOrderSummary(Request $request): JsonResponse
    {
        if (!Helpers::getCustomerInformation($request) != 'offline') {
            return response()->json([
                'success' => false,
                'message' => 'Please login'
            ], 401);
        }

        $customer = Helpers::getCustomerInformation($request);
        $cartItems = Cart::where('customer_id', $customer->id)
            ->where('is_checked', 1)
            ->get();

        $subTotal = 0;
        $totalResellProfit = 0;
        $totalResellCommission = 0;

        foreach ($cartItems as $item) {
            $subTotal += $item->price * $item->quantity;
            if ($item->is_resell) {
                $totalResellProfit += $item->resell_profit * $item->quantity;
                $totalResellCommission += $item->resell_commission * $item->quantity;
            }
        }

        // Calculate shipping cost
        $shippingCost = CartManager::get_shipping_cost(type: 'checked');
        $cashHandlingFee = $shippingCost > 0 ? ($shippingCost * 0.05) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'sub_total' => $subTotal,
                'total_shipping_cost' => $shippingCost,
                'cash_handling_fee' => $cashHandlingFee,
                'resell_profit' => $totalResellProfit,
                'resell_commission' => $totalResellCommission,
                'total_amount' => $subTotal + $totalResellProfit + $totalResellCommission + $shippingCost + $cashHandlingFee,
            ]
        ]);
    }

    /**
     * Get resellable products
     */
    public function getResellableProducts(Request $request): JsonResponse
    {
        $products = Product::where('status', 1)
            ->where('product_type', 'physical')
            ->select('id', 'name', 'slug', 'unit_price', 'thumbnail', 'discount', 'discount_type')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $products->items()->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'unit_price' => $product->unit_price,
                    'thumbnail' => $product->thumbnail,
                    'discount' => $product->discount,
                    'discount_type' => $product->discount_type,
                    'can_resell' => true,
                    'resell_info' => [
                        'base_price' => $product->unit_price,
                        'min_resell_price' => $product->unit_price,
                        'max_resell_price' => 999999.99,
                    ]
                ];
            }),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ]
        ]);
    }
}
