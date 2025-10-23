<?php

namespace Tests\Unit;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_be_created()
    {
        $client = Client::factory()->create([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'phone' => '1234567890',
            'active' => true
        ]);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('Juan Pérez', $client->name);
        $this->assertEquals('juan@example.com', $client->email);
        $this->assertEquals('1234567890', $client->phone);
        $this->assertTrue($client->active);
    }

    public function test_client_fillable_attributes()
    {
        $client = new Client();
        $fillable = $client->getFillable();

        $expectedFillable = ['name', 'email', 'phone', 'active'];
        $this->assertEquals($expectedFillable, $fillable);
    }

    public function test_client_email_is_unique()
    {
        Client::factory()->create(['email' => 'test@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Client::factory()->create(['email' => 'test@example.com']);
    }

    public function test_client_active_defaults_to_true()
    {
        $client = Client::factory()->create();
        $this->assertTrue($client->active);
    }

    public function test_client_can_be_inactive()
    {
        $client = Client::factory()->create(['active' => false]);
        $this->assertFalse($client->active);
    }

    public function test_client_phone_can_be_null()
    {
        $client = Client::factory()->create(['phone' => null]);
        $this->assertNull($client->phone);
    }

    public function test_client_scope_active()
    {
        Client::factory()->create(['active' => true]);
        Client::factory()->create(['active' => false]);

        $activeClients = Client::where('active', true)->get();
        $this->assertCount(1, $activeClients);
    }

    public function test_client_scope_inactive()
    {
        Client::factory()->create(['active' => true]);
        Client::factory()->create(['active' => false]);

        $inactiveClients = Client::where('active', false)->get();
        $this->assertCount(1, $inactiveClients);
    }
}