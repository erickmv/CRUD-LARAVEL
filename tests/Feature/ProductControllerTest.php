<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_index_page_loads()
    {
        $response = $this->get(route('products.index'));
        $response->assertStatus(200);
        $response->assertViewIs('products.index');
    }

    public function test_products_index_shows_products()
    {
        $products = Product::factory()->count(3)->create();

        $response = $this->get(route('products.index'));
        $response->assertStatus(200);
        $response->assertSee($products[0]->name);
        $response->assertSee($products[1]->sku);
    }

    public function test_products_index_with_search()
    {
        Product::factory()->create(['name' => 'Laptop Gaming']);
        Product::factory()->create(['name' => 'Mouse Inalámbrico']);

        $response = $this->get(route('products.index', ['q' => 'Laptop']));
        $response->assertStatus(200);
        $response->assertSee('Laptop Gaming');
        $response->assertDontSee('Mouse Inalámbrico');
    }

    public function test_product_can_be_created()
    {
        $productData = [
            'name' => 'Test Product',
            'sku' => 'SKU001',
            'price' => 99.99,
            'stock' => 50
        ];

        $response = $this->post(route('products.store'), $productData);
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Producto creado exitosamente');

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'sku' => 'SKU001',
            'price' => 99.99,
            'stock' => 50
        ]);
    }

    public function test_product_creation_requires_name()
    {
        $productData = [
            'sku' => 'SKU001',
            'price' => 99.99,
            'stock' => 50
        ];

        $response = $this->post(route('products.store'), $productData);
        $response->assertSessionHasErrors(['name']);
    }

    public function test_product_creation_requires_sku()
    {
        $productData = [
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 50
        ];

        $response = $this->post(route('products.store'), $productData);
        $response->assertSessionHasErrors(['sku']);
    }

    public function test_product_creation_requires_price()
    {
        $productData = [
            'name' => 'Test Product',
            'sku' => 'SKU001',
            'stock' => 50
        ];

        $response = $this->post(route('products.store'), $productData);
        $response->assertSessionHasErrors(['price']);
    }

    public function test_product_creation_requires_stock()
    {
        $productData = [
            'name' => 'Test Product',
            'sku' => 'SKU001',
            'price' => 99.99
        ];

        $response = $this->post(route('products.store'), $productData);
        $response->assertSessionHasErrors(['stock']);
    }

    public function test_product_creation_requires_unique_sku()
    {
        Product::factory()->create(['sku' => 'SKU001']);

        $productData = [
            'name' => 'Test Product',
            'sku' => 'SKU001',
            'price' => 99.99,
            'stock' => 50
        ];

        $response = $this->post(route('products.store'), $productData);
        $response->assertSessionHasErrors(['sku']);
    }

    public function test_product_price_must_be_numeric()
    {
        $productData = [
            'name' => 'Test Product',
            'sku' => 'SKU001',
            'price' => 'invalid-price',
            'stock' => 50
        ];

        $response = $this->post(route('products.store'), $productData);
        $response->assertSessionHasErrors(['price']);
    }

    public function test_product_price_must_be_minimum_zero()
    {
        $productData = [
            'name' => 'Test Product',
            'sku' => 'SKU001',
            'price' => -10,
            'stock' => 50
        ];

        $response = $this->post(route('products.store'), $productData);
        $response->assertSessionHasErrors(['price']);
    }

    public function test_product_stock_must_be_integer()
    {
        $productData = [
            'name' => 'Test Product',
            'sku' => 'SKU001',
            'price' => 99.99,
            'stock' => 'invalid-stock'
        ];

        $response = $this->post(route('products.store'), $productData);
        $response->assertSessionHasErrors(['stock']);
    }

    public function test_product_stock_must_be_minimum_zero()
    {
        $productData = [
            'name' => 'Test Product',
            'sku' => 'SKU001',
            'price' => 99.99,
            'stock' => -5
        ];

        $response = $this->post(route('products.store'), $productData);
        $response->assertSessionHasErrors(['stock']);
    }

    public function test_product_can_be_updated()
    {
        $product = Product::factory()->create();

        $updateData = [
            'name' => 'Updated Product',
            'sku' => 'SKU002',
            'price' => 199.99,
            'stock' => 100
        ];

        $response = $this->put(route('products.update', $product), $updateData);
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Producto actualizado exitosamente');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'sku' => 'SKU002',
            'price' => 199.99,
            'stock' => 100
        ]);
    }

    public function test_product_can_be_deleted()
    {
        $product = Product::factory()->create();

        $response = $this->delete(route('products.destroy', $product));
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Producto eliminado exitosamente');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_update_requires_unique_sku_except_self()
    {
        $product1 = Product::factory()->create(['sku' => 'SKU001']);
        $product2 = Product::factory()->create(['sku' => 'SKU002']);

        $updateData = [
            'name' => 'Updated Product',
            'sku' => 'SKU002', // Same as product2
            'price' => 99.99,
            'stock' => 50
        ];

        $response = $this->put(route('products.update', $product1), $updateData);
        $response->assertSessionHasErrors(['sku']);
    }

    public function test_product_update_allows_same_sku_for_same_product()
    {
        $product = Product::factory()->create(['sku' => 'SKU001']);

        $updateData = [
            'name' => 'Updated Product',
            'sku' => 'SKU001', // Same SKU
            'price' => 99.99,
            'stock' => 50
        ];

        $response = $this->put(route('products.update', $product), $updateData);
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Producto actualizado exitosamente');
    }
}