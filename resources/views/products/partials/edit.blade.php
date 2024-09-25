@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Edit Product</h1>

        <div class="col-md-8 mx-auto">
            @if (session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('products.update', $product['id']) }}" class="p-4 border rounded shadow-sm">
                @csrf
                @method('PUT')

                <!-- Product Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" required 
                           value="{{ old('name', $product['name']) }}" placeholder="Enter product name">
                </div>

                <!-- Quantity in Stock -->
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" required 
                           value="{{ old('quantity', $product['quantity']) }}" placeholder="Enter stock quantity">
                </div>

                <!-- Price per Item -->
                <div class="mb-3">
                    <label for="price" class="form-label">Price (USD)</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" required 
                           value="{{ old('price', $product['price']) }}" placeholder="Enter price per item">
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary"style="width: 100px;" >Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
