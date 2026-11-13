<?php

use App\Models\Producto;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $buscar = '';
    public string $filtro = 'todos';

    public function updatedBuscar(): void
    {
        $this->resetPage();
    }

    public function updatedFiltro(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function productos()
    {
        return Producto::with('categoria')
            ->where('nombre', 'like', '%' . $this->buscar . '%')
            ->when($this->filtro === 'bajo', fn ($q) => $q->where('stock', '>', 0)->whereColumn('stock', '<=', 'stock_minimo'))
            ->when($this->filtro === 'agotado', fn ($q) => $q->where('stock', '<=', 0))
            ->when($this->filtro === 'disponible', fn ($q) => $q->whereColumn('stock', '>', 'stock_minimo'))
            ->orderBy('nombre')
            ->paginate(15);
    }

    #[Computed]
    public function totalBajo()
    {
        return Producto::where('stock', '>', 0)->whereColumn('stock', '<=', 'stock_minimo')->count();
    }

    #[Computed]
    public function totalAgotado()
    {
        return Producto::where('stock', '<=', 0)->count();
    }
};
?>

<div class="max-w-5xl mx-auto bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Inventario</h1>
        <a href="{{ route('productos.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Nuevo producto</a>
    </div>

    @if (session('ok'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('ok') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-2 gap-4 mb-4">
        <button wire:click="$set('filtro', 'bajo')" class="text-left bg-red-50 border border-red-200 rounded p-3 hover:bg-red-100">
            <p class="text-sm text-red-700">Stock bajo</p>
            <p class="text-2xl font-bold text-red-700">{{ number_format($this->totalBajo, 0, ',', '.') }}</p>
        </button>
        <button wire:click="$set('filtro', 'agotado')" class="text-left bg-gray-50 border rounded p-3 hover:bg-gray-100">
            <p class="text-sm text-gray-600">Agotados</p>
            <p class="text-2xl font-bold text-gray-700">{{ number_format($this->totalAgotado, 0, ',', '.') }}</p>
        </button>
    </div>

    <div class="flex gap-3 mb-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="buscar"
            placeholder="Buscar producto..."
            class="flex-1 border rounded px-3 py-2"
        >
        <select wire:model.live="filtro" class="border rounded px-3 py-2">
            <option value="todos">Todos</option>
            <option value="disponible">Disponibles</option>
            <option value="bajo">Stock bajo</option>
            <option value="agotado">Agotados</option>
        </select>
    </div>

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
                    <td class="py-2">
                        <a href="{{ route('productos.show', $p) }}" class="text-green-700 hover:underline">{{ $p->nombre }}</a>
                    </td>
                    <td>{{ $p->categoria->nombre }}</td>
                    <td class="text-right">${{ number_format($p->precio_venta, 0, ',', '.') }}</td>
                    <td class="text-right">
                        @if ($p->stock <= 0)
                            <span class="text-gray-400">Agotado</span>
                        @elseif ($p->stock <= $p->stock_minimo)
                            <span class="text-red-600 font-bold">{{ rtrim(rtrim(number_format($p->stock, 2, ',', '.'), '0'), ',') }}</span>
                        @else
                            {{ rtrim(rtrim(number_format($p->stock, 2, ',', '.'), '0'), ',') }}
                        @endif
                    </td>
                    <td class="text-right whitespace-nowrap">
                        <a href="{{ route('productos.edit', $p) }}" class="text-blue-600 hover:underline">Editar</a>
                        <form method="POST" action="{{ route('productos.destroy', $p) }}" class="inline"
                              onsubmit="return confirm('¿Seguro que quieres eliminar este producto?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline ml-3">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $this->productos->links() }}
    </div>
</div>