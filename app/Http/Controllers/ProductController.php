<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private const PRODUCTS_FILE = 'products.json';

    public function index(Request $request)
    {
        // Load products and calculate total value
        $products = $this->loadProducts();
        $totalValue = $this->calculateTotalValue($products);

        // Check if the request is an AJAX request
        if ($request->ajax()) {
            // Return JSON response for AJAX requests
            if (empty($products)) {
                return response()->json([
                    'message' => 'No products available.',
                    'products' => [],
                    'totalValue' => 0
                ]);
            }

            return response()->json([
                'products' => $products,
                'totalValue' => $totalValue
            ]);
        }

        // For normal requests, return the view
        return view('products.index', compact('products', 'totalValue'));
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);
        $products = $this->loadProducts();

        if ($editId = $request->input('edit-id')) {
            $this->updateExistingProduct($products, (int)$editId, $request);
        } else {
            // Check for existing product before creating a new one
            if (!$this->isProductDuplicate($products, $request->name)) {
                $this->createNewProduct($products, $request);
            } else {
                return redirect()->route('products.index')->with('error', 'Product already exists.');
            }
        }

        $this->saveProducts($products);

        return redirect()->route('products.index')->with('success', 'Product added/updated successfully');
    }

    public function edit($id)
    {
        $product = $this->findProductById((int)$id);
        return view('products.partials.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $this->validateRequest($request);
        $products = $this->loadProducts();
        $productIndex = $this->findProductIndexById($products, (int)$id);
        
        $this->updateProduct($products, $productIndex, $request);
        $this->saveProducts($products);

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $products = $this->loadProducts();
        
        if (($productIndex = $this->findProductIndexById($products, (int)$id)) !== null) {
            unset($products[$productIndex]);
            $this->saveProducts(array_values($products));
            return redirect()->route('products.index')->with('success', 'Product deleted successfully');
        }

        return redirect()->route('products.index')->with('error', 'Product not found.');
    }

    private function loadProducts(): array
    {
        if (!Storage::exists(self::PRODUCTS_FILE)) {
            return [];
        }

        $productsJson = Storage::get(self::PRODUCTS_FILE);
        return json_decode($productsJson, true) ?? [];
    }

    private function saveProducts(array $products): void
    {
        Storage::put(self::PRODUCTS_FILE, json_encode($products, JSON_PRETTY_PRINT));
    }

    private function calculateTotalValue(array $products): float
    {
        return array_reduce($products, function ($carry, $product) {
            return isset($product['quantity'], $product['price']) ? 
                ($carry + ((int)$product['quantity'] * (float)$product['price'])) : 
                $carry;
        }, 0);
    }

    private function updateExistingProduct(array &$products, int $editId, Request $request): void
    {
        if (($productIndex = $this->findProductIndexById($products, $editId)) !== null) {
            $products[$productIndex] = $this->createProductArray($request, $editId);
        }
    }

    private function createNewProduct(array &$products, Request $request): void
    {
        $newProduct = $this->createProductArray($request);
        
        if ($newProduct) {
            array_push($products, $newProduct);
        }
    }

    private function createProductArray(Request $request, int $editId = null): array
    {
        return [
            'id' => (int) ($editId ?? $this->generateProductId()),
            'name' => trim($request->name),
            'quantity' => (int) ($request->quantity),
            'price' => (float) ($request->price),
            'datetime_submitted' => now()->toDateTimeString(),
            'total_value' => (int) ($request->quantity) * (float) ($request->price),
        ];
    }

    private function generateProductId(): int
    {
        return count($this->loadProducts()) > 0 ? max(array_column($this->loadProducts(), 'id')) + 1 : 1;
    }

    private function findProductById(int $id): ?array
    {
        return collect($this->loadProducts())->firstWhere('id', $id) ?? null;
    }

    private function findProductIndexById(array &$products, int $id): ?int
    {
        return array_search($id, array_column($products, 'id')) !== false ? 
            array_search($id, array_column($products, 'id')) : null;
    }

    private function updateProduct(array &$products, int $index, Request $request): void
    {
        if (isset($products[$index])) {
            $products[$index] =  $this->createProductArray($request, (int)$products[$index]['id']);
        }
    }

    private function validateRequest(Request $request): void
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'edit-id' => 'nullable|integer',
        ]);
    }

    // New method to check for duplicate products
    private function isProductDuplicate(array $products, string $name): bool
    {
        return collect($products)->contains(fn($product) => strtolower($product['name']) === strtolower($name));
    }
}
