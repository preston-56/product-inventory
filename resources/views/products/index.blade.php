@extends('layouts.app')

@section('title', 'ProdTrack')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Product Inventory</h1>

        <!-- Display success message -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Display error message -->
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Include the form for adding/editing products -->
        @include('products.partials.form')

        <!-- Progress bar -->
        <div class="progress mt-4" style="display: none;">
            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar"
                style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        <div id="loadingMessage" class="text-center">
            <p>Loading products...</p>
        </div>

        <div id="productTable" class="table-responsive mt-4" style="display: none;">
            <h2>Products</h2>
            <table class="table table-striped table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Date</th>
                        <th>Total Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <!-- Products will be dynamically loaded here -->
                </tbody>
            </table>
            <div class="alert alert-info">
                <strong>Total Value:</strong> <span id="totalValue">0.00</span>
            </div>
        </div> <!-- End of product table -->

        <div id="noProductsMessage" class="text-danger text-center" style="display: none;">
            <p>No products available or failed to fetch products. Please try again later.</p>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="close" onclick="closeModal()" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">Are you sure you want to delete this product?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                        <form id="deleteForm" method="POST" action="" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function closeModal() {
                $('#deleteModal').modal('hide');
            }
        </script>


        <!-- Include jQuery and custom scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>

        <script>
            $(document).ready(function() {
                loadProducts();

                function loadProducts() {
                    $('.progress').show(); // Show progress bar
                    $('#loadingMessage').show(); // Show loading message
                    $('#noProductsMessage').hide(); // Hide no products message

                    $.ajax({
                        url: '{{ url('/products') }}',
                        method: 'GET',
                        dataType: 'json',
                        beforeSend: function() {
                            $('#progressBar').css('width', '0%').attr('aria-valuenow', 0);
                        },
                        success: function(data) {
                            if (data && Array.isArray(data.products)) {
                                renderProducts(data.products);
                                $('#totalValue').text(number_format(data.totalValue, 2));
                                $('#loadingMessage').hide();
                                $('#productTable').show();

                                // Update progress bar to 100% after loading products
                                $('#progressBar').css('width', '100%').attr('aria-valuenow', 100);
                            } else {
                                handleError();
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('AJAX Error:', textStatus, errorThrown);
                            handleError();
                        },
                        complete: function() {
                            $('.progress').hide(); // Hide progress bar after loading
                        }
                    });
                }

                function handleError() {
                    $('#loadingMessage').hide();
                    $('#productTable').hide();
                    $('#noProductsMessage').show(); // Show error message
                }

                function renderProducts(products) {
                    const productTableBody = $('#productTableBody');
                    productTableBody.empty(); // Clear existing rows

                    if (products.length === 0) {
                        productTableBody.append(
                            '<tr><td colspan="6" class="text-center">No products available.</td></tr>'
                        );
                        $('#noProductsMessage').show(); // Show message for no products
                        return;
                    }

                    products.forEach((product, index) => {
                        // Validate product fields before rendering
                        const name = product.name ? product.name : 'N/A';
                        const quantity = (typeof product.quantity === 'number' && !isNaN(product.quantity)) ?
                            product.quantity : '0';
                        const price = (typeof product.price === 'number' && !isNaN(product.price)) ?
                            number_format(product.price, 2) : '0.00';
                        const dateSubmitted = product.datetime_submitted ? new Date(product.datetime_submitted)
                            .toLocaleString() : 'N/A';
                        const totalValue = (typeof product.total_value === 'number' && !isNaN(product
                            .total_value)) ? number_format(product.total_value, 2) : '0.00';

                        const row = `
                                        <tr style="background-color: ${index % 2 === 0 ? '#f8f9fa' : '#e9ecef'};">
                                            <td>${name}</td>
                                            <td>${quantity}</td>
                                            <td>${price}</td>
                                            <td>${dateSubmitted}</td>
                                            <td>${totalValue}</td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group" style="display: flex; justify-content: center; gap: 5px;">
                                                    <a href="{{ url('products') }}/${product.id}/edit" class="btn btn-warning btn-sm" style="width: 100px; border: none;">Edit</a>
                                                    <button class="btn btn-danger btn-sm delete-btn" data-id="${product.id}" style="width: 100px; border: none;">Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                        productTableBody.append(row);
                    });
                }

                // Delete button handler
                $(document).on('click', '.delete-btn', function() {
                    var productId = $(this).data('id');
                    var deleteUrl = '{{ url('products') }}' + '/' + productId;
                    $('#deleteForm').attr('action', deleteUrl);
                    $('#deleteModal').modal('show');
                });
            });

            function number_format(number, decimals = 2) {
                return parseFloat(number).toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
        </script>
    </div>
@endsection
