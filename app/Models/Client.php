<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo de Cliente
 * @author Erick Adrian Mendez Villalpando
 */
class Client extends Model
{
    use HasFactory;
    
    // Campos que se pueden llenar masivamente
    protected $fillable = ['name','email','phone','active'];
    
    /**
     * Solo clientes activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    /**
     * Clientes inactivos
     */
    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }
}
