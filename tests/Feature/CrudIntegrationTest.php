<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_crud_workflow_for_clients()
    {
        // 1. Create a client
        $clientData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'phone' => '1234567890',
            'active' => true
        ];

        $response = $this->post(route('clients.store'), $clientData);
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Cliente creado exitosamente');

        $client = Client::where('email', 'juan@example.com')->first();
        $this->assertNotNull($client);

        // 2. View clients list
        $response = $this->get(route('clients.index'));
        $response->assertStatus(200);
        $response->assertSee('Juan Pérez');
        $response->assertSee('juan@example.com');

        // 3. Update the client
        $updateData = [
            'name' => 'Juan Carlos Pérez',
            'email' => 'juan.carlos@example.com',
            'phone' => '9876543210',
            'active' => false
        ];

        $response = $this->put(route('clients.update', $client), $updateData);
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Cliente actualizado exitosamente');

        $client->refresh();
        $this->assertEquals('Juan Carlos Pérez', $client->name);
        $this->assertEquals('juan.carlos@example.com', $client->email);
        $this->assertEquals(0, $client->active);

        // 4. Delete the client
        $response = $this->delete(route('clients.destroy', $client));
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Cliente eliminado exitosamente');

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_complete_crud_workflow_for_products()
    {
        // 1. Create a product
        $productData = [
            'name' => 'Laptop Gaming',
            'sku' => 'LP001',
            'price' => 1500.00,
            'stock' => 10
        ];

        $response = $this->post(route('products.store'), $productData);
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Producto creado exitosamente');

        $product = Product::where('sku', 'LP001')->first();
        $this->assertNotNull($product);

        // 2. View products list
        $response = $this->get(route('products.index'));
        $response->assertStatus(200);
        $response->assertSee('Laptop Gaming');
        $response->assertSee('LP001');

        // 3. Update the product
        $updateData = [
            'name' => 'Laptop Gaming Pro',
            'sku' => 'LP002',
            'price' => 2000.00,
            'stock' => 5
        ];

        $response = $this->put(route('products.update', $product), $updateData);
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Producto actualizado exitosamente');

        $product->refresh();
        $this->assertEquals('Laptop Gaming Pro', $product->name);
        $this->assertEquals('LP002', $product->sku);
        $this->assertEquals(2000.00, $product->price);
        $this->assertEquals(5, $product->stock);

        // 4. Delete the product
        $response = $this->delete(route('products.destroy', $product));
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Producto eliminado exitosamente');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_dashboard_integration_with_crud_operations()
    {
        // Create initial data
        Client::factory()->count(3)->create(['active' => true]);
        Client::factory()->count(1)->create(['active' => false]);
        Product::factory()->count(2)->create(['stock' => 10, 'price' => 100]);

        // Check dashboard shows correct statistics
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('totalClients', 4);
        $response->assertViewHas('activeClients', 3);
        $response->assertViewHas('totalProducts', 2);
        $response->assertViewHas('totalStock', 20);
        $response->assertViewHas('totalValue', 2000);

        // Add more data
        $this->post(route('clients.store'), [
            'name' => 'New Client',
            'email' => 'new@example.com',
            'phone' => '1234567890',
            'active' => true
        ]);

        $this->post(route('products.store'), [
            'name' => 'New Product',
            'sku' => 'NP001',
            'price' => 50.00,
            'stock' => 5
        ]);

        // Check dashboard updates
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('totalClients', 5);
        $response->assertViewHas('activeClients', 4);
        $response->assertViewHas('totalProducts', 3);
        $response->assertViewHas('totalStock', 25);
        $response->assertViewHas('totalValue', 2250);
    }

    public function test_search_functionality_integration()
    {
        // Create test data
        Client::factory()->create(['name' => 'Juan Pérez', 'email' => 'juan@example.com']);
        Client::factory()->create(['name' => 'María García', 'email' => 'maria@example.com']);
        Product::factory()->create(['name' => 'Laptop Gaming', 'sku' => 'LP001']);
        Product::factory()->create(['name' => 'Mouse Inalámbrico', 'sku' => 'MS001']);

        // Test client search by name
        $response = $this->get(route('clients.index', ['q' => 'Juan']));
        $response->assertSee('Juan Pérez');
        $response->assertDontSee('María García');

        // Test client search by email
        $response = $this->get(route('clients.index', ['q' => 'maria@example.com']));
        $response->assertSee('María García');
        $response->assertDontSee('Juan Pérez');

        // Test product search by name
        $response = $this->get(route('products.index', ['q' => 'Laptop']));
        $response->assertSee('Laptop Gaming');
        $response->assertDontSee('Mouse Inalámbrico');

        // Test product search by SKU
        $response = $this->get(route('products.index', ['q' => 'MS001']));
        $response->assertSee('Mouse Inalámbrico');
        $response->assertDontSee('Laptop Gaming');
    }

    public function test_validation_errors_integration()
    {
        // Test client validation errors
        $response = $this->post(route('clients.store'), []);
        $response->assertSessionHasErrors(['name', 'email']);

        // Test product validation errors
        $response = $this->post(route('products.store'), []);
        $response->assertSessionHasErrors(['name', 'sku', 'price', 'stock']);

        // Test duplicate email
        Client::factory()->create(['email' => 'test@example.com']);
        $response = $this->post(route('clients.store'), [
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'active' => true
        ]);
        $response->assertSessionHasErrors(['email']);

        // Test duplicate SKU
        Product::factory()->create(['sku' => 'SKU001']);
        $response = $this->post(route('products.store'), [
            'name' => 'Test Product',
            'sku' => 'SKU001',
            'price' => 99.99,
            'stock' => 50
        ]);
        $response->assertSessionHasErrors(['sku']);
    }

    public function test_pagination_integration()
    {
        // Create more than 10 clients and products
        Client::factory()->count(15)->create();
        Product::factory()->count(12)->create();

        // Test clients pagination
        $response = $this->get(route('clients.index'));
        $response->assertStatus(200);
        $clients = $response->viewData('clients');
        $this->assertCount(10, $clients); // Default pagination size

        // Test products pagination
        $response = $this->get(route('products.index'));
        $response->assertStatus(200);
        $products = $response->viewData('products');
        $this->assertCount(10, $products); // Default pagination size
    }
}