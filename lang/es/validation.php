<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'email' => 'El campo :attribute debe ser un correo electrónico válido.',
    'unique' => 'El valor de :attribute ya está registrado.',
    'exists' => 'El valor seleccionado en :attribute no es válido.',
    'in' => 'El valor seleccionado en :attribute no es válido.',

    'max' => [
        'numeric' => 'El campo :attribute no puede ser mayor que :max.',
        'string' => 'El campo :attribute no puede tener más de :max caracteres.',
        'array' => 'El campo :attribute no puede tener más de :max elementos.',
        'file' => 'El archivo :attribute no puede pesar más de :max kilobytes.',
    ],
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El archivo :attribute debe pesar al menos :min kilobytes.',
    ],
    'gt' => [
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'string' => 'El campo :attribute debe tener más de :value caracteres.',
    ],

    'attributes' => [
        'nombre' => 'nombre',
        'descripcion' => 'descripción',
        'codigo_barras' => 'código de barras',
        'categoria_id' => 'categoría',
        'proveedor_id' => 'proveedor',
        'producto_id' => 'producto',
        'precio_compra' => 'precio de compra',
        'precio_venta' => 'precio de venta',
        'stock' => 'stock',
        'stock_minimo' => 'stock mínimo',
        'telefono' => 'teléfono',
        'email' => 'correo',
        'direccion' => 'dirección',
        'cantidad' => 'cantidad',
        'tipo' => 'tipo',
        'motivo' => 'motivo',
    ],
];