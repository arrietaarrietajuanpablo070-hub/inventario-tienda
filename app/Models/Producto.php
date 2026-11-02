<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'stock' => 'decimal:2',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}