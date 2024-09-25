<form id="productForm" method="POST" action="{{ url('products') }}">
    @csrf
    <input type="hidden" id="edit-id" name="edit-id" value="">

    <div class="form-group">
        <label for="productName">Product Name</label>
        <input type="text" class="form-control" id="productName" name="name" required>
    </div>

    <div class="form-group">
        <label for="productQuantity">Quantity in Stock</label>
        <input type="number" class="form-control" id="productQuantity" name="quantity" required>
    </div>

    <div class="form-group">
        <label for="productPrice">Price/item (USD) </label>
        <input type="number" class="form-control" id="productPrice" name="price" step="0.01" required>
    </div>

    <button type="submit" class="btn btn-primary">Add Product</button>
</form>
