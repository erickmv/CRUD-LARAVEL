<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo de Producto
 * @author Erick Adrian Mendez Villalpando
 */
class Product extends Model
{
    use HasFactory;
    
    // Campos que se pueden llenar masivamente
    protected $fillable = ['name','sku','price','stock'];
    
    /**
     * Valor total del producto
     */
    public function getTotalValueAttribute()
    {
        return $this->price * $this->stock;
    }
}
