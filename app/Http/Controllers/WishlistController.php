<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display the wishlist page.
     */
    public function index()
    {
        $wishlist = auth()->user()->wishlist()->with('items.product')->first();
        
        return view('wishlist.index', compact('wishlist'));
    }

    /**
     * Add a product to the wishlist.
     */
    public function add(Product $product)
    {
        $wishlist = auth()->user()->wishlist()->firstOrCreate([]);

        // Check if product already exists in wishlist
        if ($wishlist->hasProduct($product->id)) {
            return back()->with('info', 'Product is already in your wishlist.');
        }

        $wishlist->items()->create([
            'product_id' => $product->id
        ]);

        return back()->with('success', 'Product added to wishlist.');
    }

    /**
     * Remove a product from the wishlist.
     */
    public function remove(Product $product)
    {
        $wishlist = auth()->user()->wishlist;
        
        if ($wishlist) {
            $wishlist->items()->where('product_id', $product->id)->delete();
        }

        return back()->with('success', 'Product removed from wishlist.');
    }

    /**
     * Move product from wishlist to cart.
     */
    public function moveToCart(Product $product)
    {
        $wishlist = auth()->user()->wishlist;
        $cart = auth()->user()->cart()->firstOrCreate([]);

        // Remove from wishlist
        if ($wishlist) {
            $wishlist->items()->where('product_id', $product->id)->delete();
        }

        // Add to cart
        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            if ($item->quantity < $product->stock) {
                $item->increment('quantity');
            }
        } else {
            if ($product->stock > 0) {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => 1
                ]);
            }
        }

        return redirect()->route('cart.index')->with('success', 'Product moved to cart.');
    }
}
