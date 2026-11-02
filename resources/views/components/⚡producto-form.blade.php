<?php

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public ?Producto $producto = null;

    public $nombre = '';
    public $codigo_barras = '';
    public $categoria_id = '';
    public $precio_compra = 0;
    public $precio_venta = 0;
    public $stock = 0;
    public $stock_minimo = 5;

    public function mount(?Producto $producto = null): void
    {
        if ($producto && $producto->exists) {
            $this->producto = $producto;
            $this->nombre = $producto->nombre;
            $this->codigo_barras = $producto->codigo_barras ?? '';
            $this->categoria_id = $producto->categoria_id;
            $this->precio_compra = $producto->precio_compra;
            $this->precio_venta = $producto->precio_venta;
            $this->stock = $producto->stock;
            $this->stock_minimo = $producto->stock_minimo;
        }
    }

    protected function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'codigo_barras' => [
                'nullable', 'string', 'max:255',
                Rule::unique('productos', 'codigo_barras')->ignore($this->producto?->id),
            ],
            'categoria_id' => 'required|exists:categorias,id',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
        ];
    }

    #[Computed]
    public function categorias()
    {
        return Categoria::orderBy('nombre')->get();
    }

    public function guardar()
    {
        $datos = $this->validate();
        $datos['codigo_barras'] = $datos['codigo_barras'] ?: null;

        if ($this->producto) {
            $this->producto->update($datos);
            session()->flash('ok', 'Producto actualizado correctamente.');
        } else {
            Producto::create($datos);
            session()->flash('ok', 'Producto creado correctamente.');
        }

        return $this->redirect('/productos');
    }
};
?>

<div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-4">{{ $producto ? 'Editar producto' : 'Nuevo producto' }}</h1>

    <form wire:submit="guardar" class="space-y-4">
        <div>
            <label class="block text-sm font-semibold">Nombre</label>
            <input type="text" wire:model="nombre" class="w-full border rounded px-3 py-2">
            @error('nombre') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold">Código de barras (opcional)</label>
            <input type="text" wire:model="codigo_barras" class="w-full border rounded px-3 py-2">
            @error('codigo_barras') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold">Categoría</label>
            <select wire:model="categoria_id" class="w-full border rounded px-3 py-2">
                <option value="">Selecciona una...</option>
                @foreach ($this->categorias as $c)
                    <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                @endforeach
            </select>
            @error('categoria_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold">Precio de compra</label>
                <input type="number" step="0.01" wire:model="precio_compra" class="w-full border rounded px-3 py-2">
                @error('precio_compra') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold">Precio de venta</label>
                <input type="number" step="0.01" wire:model="precio_venta" class="w-full border rounded px-3 py-2">
                @error('precio_venta') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold">Stock</label>
                <input type="number" step="0.01" wire:model="stock" class="w-full border rounded px-3 py-2">
                @error('stock') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold">Stock mínimo</label>
                <input type="number" wire:model="stock_minimo" class="w-full border rounded px-3 py-2">
                @error('stock_minimo') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar</button>
            <a href="/productos" class="px-4 py-2 rounded border">Cancelar</a>
        </div>
    </form>
</div>