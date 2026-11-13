<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['activo' => $this->boolean('activo')]);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'codigo_barras' => [
                'nullable', 'string', 'max:255',
                Rule::unique('productos', 'codigo_barras')->ignore($this->route('producto')),
            ],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'proveedor_id' => ['nullable', 'exists:proveedores,id'],
            'precio_compra' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'numeric', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'activo' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'codigo_barras.unique' => 'Ese código de barras ya pertenece a otro producto.',
            'categoria_id.required' => 'Selecciona una categoría.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'stock_minimo.integer' => 'El stock mínimo debe ser un número entero.',
        ];
    }
}
