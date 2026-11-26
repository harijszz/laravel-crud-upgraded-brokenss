<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:products|max:255',
            'quantity' => 'required|integer',
            'description' => 'required',
        ]);

        $product = Product::create($validated);

        return redirect()
            ->route('products/show', [$product]) // vai ['product' => $product]
            ->with('message', 'Product created successfully');
    }

    public function show(Product $product)
    {
        return view('products/show', compact('product'));
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products/index')
            ->with('message', 'Product deleted successfully');
    }

    public function edit(Product $product)
    {
        return view('products/edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|unique:products,name,'.$product->id.'|max:255',
            'quantity' => 'sometimes|required|integer',
            'description' => 'sometimes|required',
            'tags' => 'nullable|array',
            'tags.*' => 'required|string|max:255',
        ]);

        $tagIds = collect($validated['tags'] ?? [])
            ->map(fn ($title) => trim($title))
            ->filter()
            ->unique()
            ->map(fn ($title) => Tag::firstOrCreate(['title' => $title])->id)
            ->all();

        unset($validated['tags']);

        $product->update($validated);
        $product->tags()->sync($tagIds);

        return redirect()
            ->route('products/show', [$product])
            ->with('message', 'Product updated successfully');
    }

    public function decreaseQuantity(Request $request, Product $product)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        if ($product->decreaseQuantity($request->amount)) {
            return redirect()
                ->route('products/show', [$product])
                ->with('message', 'Product quantity decreased successfully');
        } else {
            return redirect()
                ->route('products/show', [$product])
                ->withErrors(['amount' => 'Not enough stock to decrease quantity.']);
        }
    }
    public function updateQuantity(Request $request, Product $product)
{
    $request->validate([
        'action' => 'required|in:increase,decrease'
    ]);

    if ($request->action == 'increase') {
        $product->increment('quantity');
    } else {
        if ($product->quantity > 0) {
            $product->decrement('quantity');
        }
    }

    return response()->json([
        'quantity' => $product->fresh()->quantity
    ]);
}
}
