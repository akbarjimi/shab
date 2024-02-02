<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function addToCart(Product $product, $quantity = 1)
    {
        try {
            DB::beginTransaction();
            $user = Auth::user();

            $cart = $this->cartService->getActiveCart($user);

            if (!$product->isQuantityAboveThreshold($quantity)) {
                return response()->json(['error' => 'Product quantity exceeds threshold'], 422);
            }

            $this->cartService->addProductToCart($cart, $product, $quantity);

            DB::commit();
            return response()->json(['message' => 'Product added to cart successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return response()->json(['error' => 'Failed to add product to cart'], 500);
        }
    }

    public function updateCartItem(UpdateCartItemRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $product = $request->validated()['product'];
            $quantity = $request->validated()['quantity'];

            $cart = $this->cartService->updateQuantity(Auth::user(), Product::find($product), $quantity);

            DB::commit();
            return response()->json(['cart' => $cart]);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return response()->json(['error' => 'Failed to update cart item'], 500);
        }
    }

    public function convertCartToOrder(Request $request): JsonResponse
    {
        try {
            $order = $this->cartService->convertToOrder(Auth::user());

            return response()->json(['order' => $order]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['error' => 'Failed to convert cart to order'], 500);
        }
    }

    public function checkout(): JsonResponse
    {
        try {
            $order = $this->cartService->checkout(Auth::user());

            return response()->json(['order' => $order]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['error' => 'Failed to checkout'], 500);
        }
    }

    public function removeFromCart(Product $product)
    {
        try {
            $user = Auth::user();

            $cart = $user->carts()->where('status', 'active')->first();

            if ($cart) {
                $this->cartService->removeFromCart($user, $product);

                return response()->json(['message' => 'Product removed from cart successfully', 'cart' => $cart]);
            }

            return response()->json(['message' => 'User does not have an active cart']);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['error' => 'Failed to remove product from cart'], 500);
        }
    }
}
