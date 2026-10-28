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
    <h1 class="text-2xl font-bold mb-4">Inventario</h1>

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
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $this->productos->links() }}
    </div>
</div>