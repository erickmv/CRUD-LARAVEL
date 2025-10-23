<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_page_loads()
    {
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    public function test_dashboard_shows_statistics()
    {
        // Create test data
        Client::factory()->count(5)->create(['active' => true]);
        Client::factory()->count(2)->create(['active' => false]);
        Product::factory()->count(3)->create(['stock' => 10, 'price' => 100]);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        
        // Check if statistics are passed to view
        $response->assertViewHas('totalClients', 7);
        $response->assertViewHas('activeClients', 5);
        $response->assertViewHas('totalProducts', 3);
        $response->assertViewHas('totalStock', 30);
        $response->assertViewHas('totalValue', 3000);
    }

    public function test_dashboard_shows_clients_by_month()
    {
        // Create clients with different creation dates
        Client::factory()->create(['created_at' => now()->subMonths(1)]);
        Client::factory()->create(['created_at' => now()->subMonths(2)]);
        Client::factory()->create(['created_at' => now()->subMonths(3)]);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('clientsByMonth');
        
        $clientsByMonth = $response->viewData('clientsByMonth');
        $this->assertCount(3, $clientsByMonth);
    }

    public function test_dashboard_shows_products_by_month()
    {
        // Create products with different creation dates
        Product::factory()->create(['created_at' => now()->subMonths(1)]);
        Product::factory()->create(['created_at' => now()->subMonths(2)]);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('productsByMonth');
        
        $productsByMonth = $response->viewData('productsByMonth');
        $this->assertCount(2, $productsByMonth);
    }

    public function test_dashboard_shows_top_products()
    {
        // Create products with different stock levels
        Product::factory()->create(['name' => 'High Stock', 'stock' => 100]);
        Product::factory()->create(['name' => 'Medium Stock', 'stock' => 50]);
        Product::factory()->create(['name' => 'Low Stock', 'stock' => 10]);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('topProducts');
        
        $topProducts = $response->viewData('topProducts');
        $this->assertCount(3, $topProducts);
        $this->assertEquals('High Stock', $topProducts->first()->name);
    }

    public function test_dashboard_shows_clients_status()
    {
        Client::factory()->count(3)->create(['active' => true]);
        Client::factory()->count(2)->create(['active' => false]);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('clientsStatus');
        
        $clientsStatus = $response->viewData('clientsStatus');
        $this->assertEquals(3, $clientsStatus['active']);
        $this->assertEquals(2, $clientsStatus['inactive']);
    }

    public function test_dashboard_handles_empty_data()
    {
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        
        // Check if statistics are zero when no data exists
        $response->assertViewHas('totalClients', 0);
        $response->assertViewHas('activeClients', 0);
        $response->assertViewHas('totalProducts', 0);
        $response->assertViewHas('totalStock', 0);
        $response->assertViewHas('totalValue', 0);
    }

    public function test_dashboard_calculates_total_value_correctly()
    {
        // Create products with specific prices and stock
        Product::factory()->create(['price' => 100, 'stock' => 5]); // 500
        Product::factory()->create(['price' => 50, 'stock' => 10]); // 500
        Product::factory()->create(['price' => 200, 'stock' => 2]); // 400

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        
        $totalValue = $response->viewData('totalValue');
        $this->assertEquals(1400, $totalValue); // 500 + 500 + 400
    }

    public function test_dashboard_only_shows_last_6_months_data()
    {
        // Create old data (more than 6 months)
        Client::factory()->create(['created_at' => now()->subMonths(7)]);
        Product::factory()->create(['created_at' => now()->subMonths(8)]);

        // Create recent data (within 6 months)
        Client::factory()->create(['created_at' => now()->subMonths(3)]);
        Product::factory()->create(['created_at' => now()->subMonths(2)]);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        
        $clientsByMonth = $response->viewData('clientsByMonth');
        $productsByMonth = $response->viewData('productsByMonth');
        
        // Should only include recent data
        $this->assertCount(1, $clientsByMonth);
        $this->assertCount(1, $productsByMonth);
    }

    public function test_dashboard_limits_top_products_to_5()
    {
        // Create 7 products
        Product::factory()->count(7)->create();

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        
        $topProducts = $response->viewData('topProducts');
        $this->assertCount(5, $topProducts);
    }
}