<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the cart page.
     */
    public function index()
    {
        $cart = auth()->user()->cart()->with('items.product')->first();
        
        return view('cart.index', compact('cart'));
    }

    /**
     * Add a product to the cart.
     */
    public function add(Product $product)
    {
        $cart = auth()->user()->cart()->firstOrCreate([]);

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            // Check stock before incrementing
            if ($item->quantity >= $product->stock) {
                return back()->with('error', 'Cannot add more. Stock limit reached.');
            }
            $item->increment('quantity');
        } else {
            // Check if product is in stock
            if ($product->stock < 1) {
                return back()->with('error', 'Product is out of stock.');
            }
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => 1
            ]);
        }

        return back()->with('success', 'Product added to cart.');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(CartItem $item)
    {
        // Ensure the item belongs to the authenticated user's cart
        if ($item->cart->user_id !== auth()->id()) {
            abort(403);
        }

        $item->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    /**
     * Update the quantity of a cart item.
     */
    public function updateQuantity(Request $request, CartItem $item)
    {
        // Ensure the item belongs to the authenticated user's cart
        if ($item->cart->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Check stock limit
        if ($validated['quantity'] > $item->product->stock) {
            return back()->with('error', 'Quantity exceeds available stock.');
        }

        $item->update(['quantity' => $validated['quantity']]);

        return back()->with('success', 'Quantity updated.');
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        $cart = auth()->user()->cart;
        
        if ($cart) {
            $cart->items()->delete();
        }

        return back()->with('success', 'Cart cleared.');
    }
}
