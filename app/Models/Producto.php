<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    protected $fillable = [
        'nombre', 'codigo_barras', 'descripcion',
        'precio_compra', 'precio_venta',
        'stock', 'stock_minimo', 'imagen', 'activo',
        'categoria_id', 'proveedor_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }
}