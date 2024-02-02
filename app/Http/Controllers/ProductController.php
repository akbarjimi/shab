<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductSearchRequest;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function store(ProductRequest $request)
    {
        $user = Auth::user();

        /** @var Product $product */
        $product = $user->products()->create([
            'title' => $request->input('title'),
            'price' => $request->input('price'),
            'shipping_cost' => $request->input('shipping_cost', 0),
        ]);

        $product->addMediaFromRequest('photos')
            ->toMediaCollection();

        $this->cacheProduct($product);

        return response()->json(['message' => 'Product created successfully'], 201);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $validatedData = $request->validated();

        $product->update($validatedData);

        if ($request->hasFile('photos')) {
            $product->clearMediaCollection();
            $product->addMultipleMediaFromRequest(['photos'])->each(function ($fileAdder) {
                $fileAdder->toMediaLibrary();
            });
        }

        $this->clearProductCache($product);
        
        return response()->json(['message' => 'Product updated successfully']);
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        try {
            DB::beginTransaction();

            $product->clearMediaCollection();

            $product->delete();

            DB::commit();

            return response()->json(['message' => 'Product deleted successfully']);
        } catch (Exception $e) {
            DB::rollBack();

            report($e);

            return response()->json(['error' => 'Product deletion failed'], 500);
        }
    }

    public function search(ProductSearchRequest $request)
    {
        $validatedData = $request->validated();

        $title = $validatedData['title'];
        $maxPrice = $validatedData['maxPrice'];
        $sortBy = $validatedData['sortBy'] ?? 'price';
        $receiveAtHome = $validatedData['receiveAtHome'] ?? false;

        $cacheKey = "product_search_{$title}_{$maxPrice}_{$sortBy}_{$receiveAtHome}";

        $products = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($title, $maxPrice, $sortBy, $receiveAtHome) {
            $query = Product::query();

            if ($title) {
                $query->where('title', 'like', "%$title%");
            }

            if ($maxPrice) {
                if ($receiveAtHome) {
                    $query->whereRaw('price + shipping_cost <= ?', [$maxPrice]);
                } else {
                    $query->where('price', '<=', $maxPrice);
                }
            }

            $query->orderBy($sortBy, 'asc');

            if ($receiveAtHome) {
                $query->select(['id', 'title', 'price', 'shipping_cost', DB::raw('price + shipping_cost as total_price')]);
            }

            return $query->get();
        });

        return response()->json(['products' => $products]);
    }

    private function cacheProduct(Product $product)
    {
        Cache::put("product_{$product->id}", $product, now()->addMinutes(60));
    }

    private function clearProductCache(Product $product)
    {
        Cache::forget("product_{$product->id}");
    }
}
