<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Controlador de productos
 * @author Erick Adrian Mendez Villalpando
 */
class ProductController extends Controller
{
    /**
     * Lista productos con búsqueda
     */
    public function index(Request $request)
    {
        $q = $request->get('q');
        $products = Product::when($q, fn($qb) =>
                $qb->where('name','like',"%$q%")->orWhere('sku','like',"%$q%")
            )
            ->latest()->paginate(10)->withQueryString();

        return view('products.index', compact('products','q'));
    }

    /**
     * Crear producto nuevo
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required','string','max:255'],
            'sku'   => ['required','string','max:100','unique:products,sku'],
            'price' => ['required','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
        ]);
        
        Product::create($data);

        return back()->with('ok','Producto creado exitosamente');
    }

    /**
     * Actualizar producto
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'  => ['required','string','max:255'],
            'sku'   => ['required','string','max:100','unique:products,sku,'.$product->id],
            'price' => ['required','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
        ]);
        
        $product->update($data);

        return back()->with('ok','Producto actualizado exitosamente');
    }

    /**
     * Borrar producto
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('ok','Producto eliminado exitosamente');
    }
}
