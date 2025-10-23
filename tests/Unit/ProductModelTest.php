<?php

namespace Tests\Unit;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created()
    {
        $product = Product::factory()->create([
            'name' => 'Laptop Gaming',
            'sku' => 'LP001',
            'price' => 1500.00,
            'stock' => 10
        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Laptop Gaming', $product->name);
        $this->assertEquals('LP001', $product->sku);
        $this->assertEquals(1500.00, $product->price);
        $this->assertEquals(10, $product->stock);
    }

    public function test_product_fillable_attributes()
    {
        $product = new Product();
        $fillable = $product->getFillable();

        $expectedFillable = ['name', 'sku', 'price', 'stock'];
        $this->assertEquals($expectedFillable, $fillable);
    }

    public function test_product_sku_is_unique()
    {
        Product::factory()->create(['sku' => 'SKU001']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Product::factory()->create(['sku' => 'SKU001']);
    }

    public function test_product_price_defaults_to_zero()
    {
        $product = Product::factory()->create(['price' => 0]);
        $this->assertEquals(0, $product->price);
    }

    public function test_product_stock_defaults_to_zero()
    {
        $product = Product::factory()->create(['stock' => 0]);
        $this->assertEquals(0, $product->stock);
    }

    public function test_product_price_can_be_decimal()
    {
        $product = Product::factory()->create(['price' => 99.99]);
        $this->assertEquals(99.99, $product->price);
    }

    public function test_product_stock_must_be_integer()
    {
        $product = Product::factory()->create(['stock' => 50]);
        $this->assertIsInt($product->stock);
        $this->assertEquals(50, $product->stock);
    }

    public function test_product_can_have_zero_stock()
    {
        $product = Product::factory()->create(['stock' => 0]);
        $this->assertEquals(0, $product->stock);
    }

    public function test_product_can_have_high_stock()
    {
        $product = Product::factory()->create(['stock' => 1000]);
        $this->assertEquals(1000, $product->stock);
    }

    public function test_product_total_value_calculation()
    {
        $product = Product::factory()->create([
            'price' => 100.00,
            'stock' => 5
        ]);

        $totalValue = $product->price * $product->stock;
        $this->assertEquals(500.00, $totalValue);
    }
}