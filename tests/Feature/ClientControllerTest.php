<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_clients_index_page_loads()
    {
        $response = $this->get(route('clients.index'));
        $response->assertStatus(200);
        $response->assertViewIs('clients.index');
    }

    public function test_clients_index_shows_clients()
    {
        $clients = Client::factory()->count(3)->create();

        $response = $this->get(route('clients.index'));
        $response->assertStatus(200);
        $response->assertSee($clients[0]->name);
        $response->assertSee($clients[1]->email);
    }

    public function test_clients_index_with_search()
    {
        Client::factory()->create(['name' => 'Juan Pérez']);
        Client::factory()->create(['name' => 'María García']);

        $response = $this->get(route('clients.index', ['q' => 'Juan']));
        $response->assertStatus(200);
        $response->assertSee('Juan Pérez');
        $response->assertDontSee('María García');
    }

    public function test_client_can_be_created()
    {
        $clientData = [
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'active' => true
        ];

        $response = $this->post(route('clients.store'), $clientData);
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Cliente creado exitosamente');

        $this->assertDatabaseHas('clients', [
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'active' => true
        ]);
    }

    public function test_client_creation_requires_name()
    {
        $clientData = [
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'active' => true
        ];

        $response = $this->post(route('clients.store'), $clientData);
        $response->assertSessionHasErrors(['name']);
    }

    public function test_client_creation_requires_email()
    {
        $clientData = [
            'name' => 'Test Client',
            'phone' => '1234567890',
            'active' => true
        ];

        $response = $this->post(route('clients.store'), $clientData);
        $response->assertSessionHasErrors(['email']);
    }

    public function test_client_creation_requires_valid_email()
    {
        $clientData = [
            'name' => 'Test Client',
            'email' => 'invalid-email',
            'phone' => '1234567890',
            'active' => true
        ];

        $response = $this->post(route('clients.store'), $clientData);
        $response->assertSessionHasErrors(['email']);
    }

    public function test_client_creation_requires_unique_email()
    {
        Client::factory()->create(['email' => 'test@example.com']);

        $clientData = [
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'active' => true
        ];

        $response = $this->post(route('clients.store'), $clientData);
        $response->assertSessionHasErrors(['email']);
    }

    public function test_client_can_be_updated()
    {
        $client = Client::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '9876543210',
            'active' => false
        ];

        $response = $this->put(route('clients.update', $client), $updateData);
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Cliente actualizado exitosamente');

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '9876543210',
            'active' => 0
        ]);
    }

    public function test_client_can_be_deleted()
    {
        $client = Client::factory()->create();

        $response = $this->delete(route('clients.destroy', $client));
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Cliente eliminado exitosamente');

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_client_update_requires_unique_email_except_self()
    {
        $client1 = Client::factory()->create(['email' => 'client1@example.com']);
        $client2 = Client::factory()->create(['email' => 'client2@example.com']);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'client2@example.com', // Same as client2
            'phone' => '1234567890',
            'active' => true
        ];

        $response = $this->put(route('clients.update', $client1), $updateData);
        $response->assertSessionHasErrors(['email']);
    }

    public function test_client_update_allows_same_email_for_same_client()
    {
        $client = Client::factory()->create(['email' => 'test@example.com']);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'test@example.com', // Same email
            'phone' => '1234567890',
            'active' => true
        ];

        $response = $this->put(route('clients.update', $client), $updateData);
        $response->assertRedirect();
        $response->assertSessionHas('ok', 'Cliente actualizado exitosamente');
    }
}