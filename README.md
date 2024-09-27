
## ProdTack

#### Overview
This Laravel project provides a form to submit product data, which is saved in a JSON file. The submitted data is displayed in a table with calculated values and the ability to edit and delete entries.

---

Installation

1. Clone the Repository
   
   ```bash
   git clone git@github.com:preston-56/product-inventory.git

   cd product-inventory

   ```

2. Install Dependencies
   
   ```bash
   composer install

   ```

3. Set up the environment
   
   ```bash
   cp .env.example .env
   php artisan key:generate

   ```
4. Start the server
   
   ```bash
   php artisan serve

   ```

### Features
- Product Form: Fields for Product 
- Name, Quantity, Price.
- JSON Storage: Data is saved to a JSON file.
- AJAX Submission: Form data submitted via AJAX.
- Display Table: Shows submitted products, including total value (Quantity * Price).
- Sum Total: Displays sum total of all products' values.
- Edit Functionality: Option to edit submitted data.
- Delete Functionality: Option to delete submitted data.

### Routes
```bash
`Route::resource('products', ProductController::class);`
- This route sets up the following endpoints automatically:
- GET /products: Display all products (index).
- POST /products: Store a new product (store).
- GET /products/{id}/edit: Edit a product (edit).
- PUT /products/{id}: Update an existing product (update).
- DELETE /products/{id}: Delete a product (destroy).
```

---

#### Product Management Feature

<div style="text-align: center;">
    <img src="./public/products.gif" alt="Product Management Feature" width="640" height="360">
</div>


### Usage
- Submit product data via the form.
- Data is saved to `products.json`and displayed in a table.
- The table is updated dynamically using AJAX.
