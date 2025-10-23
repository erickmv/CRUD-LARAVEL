<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

/**
 * Controlador para la gestión de clientes
 * Maneja todas las operaciones CRUD para el módulo de clientes
 * 
 * @author Erick Adrian Mendez Villalpando
 * @version 1.0
 */
class ClientController extends Controller
{
    /**
     * Lista clientes con búsqueda
     */
    public function index(Request $request)
    {
        $q = $request->get('q');
        
        $clients = Client::when($q, fn($qb) =>
                $qb->where('name','like',"%$q%")->orWhere('email','like',"%$q%")
            )
            ->latest()->paginate(10)->withQueryString();

        return view('clients.index', compact('clients','q'));
    }

    /**
     * Crear nuevo cliente
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => ['required','string','max:255'],
            'email'  => ['required','email','unique:clients,email'],
            'phone'  => ['nullable','string','max:30'],
            'active' => ['nullable','boolean'],
        ]);
        
        $data['active'] = $request->boolean('active');
        Client::create($data);

        return back()->with('ok','Cliente creado exitosamente');
    }

    /**
     * Actualizar cliente existente
     */
    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name'   => ['required','string','max:255'],
            'email'  => ['required','email','unique:clients,email,'.$client->id],
            'phone'  => ['nullable','string','max:30'],
            'active' => ['nullable','boolean'],
        ]);
        
        $data['active'] = $request->boolean('active');
        $client->update($data);

        return back()->with('ok','Cliente actualizado exitosamente');
    }

    /**
     * Eliminar cliente
     */
    public function destroy(Client $client)
    {
        $client->delete();
        return back()->with('ok','Cliente eliminado exitosamente');
    }
}
L