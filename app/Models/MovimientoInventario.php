<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'producto_id', 'user_id', 'tipo', 'cantidad',
        'stock_anterior', 'stock_nuevo', 'motivo',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'stock_anterior' => 'decimal:2',
        'stock_nuevo' => 'decimal:2',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}