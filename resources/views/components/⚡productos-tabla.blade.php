<?php

use App\Models\Producto;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $buscar = '';

    public function updatedBuscar(): void
    {
        $this->resetPage();
    }

    public function eliminar(int $id): void
    {
        Producto::findOrFail($id)->delete();
        session()->now('ok', 'Producto eliminado.');
    }

    #[Computed]
    public function productos()
    {
        return Producto::with('categoria')
            ->where('nombre', 'like', '%' . $this->buscar . '%')
            ->orderBy('nombre')
            ->paginate(15);
    }
};
?>

<div class="max-w-5xl mx-auto bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Inventario</h1>
        <a href="/productos/crear" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Nuevo producto</a>
    </div>

    @if (session('ok'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('ok') }}</div>
    @endif

    <input
        type="text"
        wire:model.live.debounce.300ms="buscar"
        placeholder="Buscar producto..."
        class="w-full border rounded px-3 py-2 mb-4"
    >

    <table class="w-full text-left">
        <thead>
            <tr class="border-b font-semibold">
                <th class="py-2">Producto</th>
                <th>Categoría</th>
                <th class="text-right">Precio venta</th>
                <th class="text-right">Stock</th>
                <th class="text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($this->productos as $p)
                <tr class="border-b" wire:key="producto-{{ $p->id }}">
                    <td class="py-2">{{ $p->nombre }}</td>
                    <td>{{ $p->categoria->nombre }}</td>
                    <td class="text-right">${{ number_format($p->precio_venta, 0, ',', '.') }}</td>
                    <td class="text-right {{ $p->stock <= $p->stock_minimo ? 'text-red-600 font-bold' : '' }}">
                        {{ rtrim(rtrim(number_format($p->stock, 2, ',', '.'), '0'), ',') }}
                    </td>
                    <td class="text-right whitespace-nowrap">
                        <a href="/productos/{{ $p->id }}/editar" class="text-blue-600 hover:underline">Editar</a>
                        <button
                            wire:click="eliminar({{ $p->id }})"
                            wire:confirm="¿Seguro que quieres eliminar este producto?"
                            class="text-red-600 hover:underline ml-3"
                        >Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $this->productos->links() }}
    </div>
</div>