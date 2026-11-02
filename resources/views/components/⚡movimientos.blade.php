<?php

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $buscar = '';
    public ?int $producto_id = null;
    public string $tipo = 'entrada';
    public $cantidad = '';
    public string $motivo = '';

    #[Computed]
    public function resultados()
    {
        if (strlen($this->buscar) < 2 || $this->producto_id) {
            return collect();
        }

        return Producto::where('nombre', 'like', '%' . $this->buscar . '%')
            ->orderBy('nombre')
            ->limit(8)
            ->get();
    }

    #[Computed]
    public function seleccionado()
    {
        return $this->producto_id ? Producto::find($this->producto_id) : null;
    }

    #[Computed]
    public function movimientos()
    {
        return MovimientoInventario::with('producto')->latest()->limit(20)->get();
    }

    public function seleccionar(int $id): void
    {
        $this->producto_id = $id;
        $this->buscar = '';
    }

    public function quitar(): void
    {
        $this->producto_id = null;
    }

    public function guardar(): void
    {
        $this->validate([
            'producto_id' => 'required|exists:productos,id',
            'tipo' => 'required|in:entrada,salida,ajuste',
            'cantidad' => $this->tipo === 'ajuste' ? 'required|numeric|min:0' : 'required|numeric|gt:0',
            'motivo' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () {
            $producto = Producto::lockForUpdate()->findOrFail($this->producto_id);
            $antes = (float) $producto->stock;
            $cant = (float) $this->cantidad;

            $nuevo = match ($this->tipo) {
                'entrada' => $antes + $cant,
                'salida' => $antes - $cant,
                'ajuste' => $cant,
            };

            if ($nuevo < 0) {
                throw ValidationException::withMessages([
                    'cantidad' => 'No hay stock suficiente. Stock actual: ' . $antes,
                ]);
            }

            $producto->update(['stock' => $nuevo]);

            MovimientoInventario::create([
                'producto_id' => $producto->id,
                'tipo' => $this->tipo,
                'cantidad' => abs($nuevo - $antes),
                'stock_anterior' => $antes,
                'stock_nuevo' => $nuevo,
                'motivo' => $this->motivo ?: null,
            ]);
        });

        $this->reset(['buscar', 'producto_id', 'cantidad', 'motivo']);
        $this->tipo = 'entrada';
        session()->now('ok', 'Movimiento registrado y stock actualizado.');
    }
};
?>

<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Movimientos de inventario</h1>
            <a href="/productos" class="text-blue-600 hover:underline">← Volver al inventario</a>
        </div>

        @if (session('ok'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('ok') }}</div>
        @endif

        <form wire:submit="guardar" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold">Producto</label>

                @if ($this->seleccionado)
                    <div class="flex justify-between items-center border rounded px-3 py-2 bg-gray-50">
                        <span>
                            {{ $this->seleccionado->nombre }}
                            <span class="text-gray-500">(stock actual: {{ rtrim(rtrim(number_format($this->seleccionado->stock, 2, ',', '.'), '0'), ',') }})</span>
                        </span>
                        <button type="button" wire:click="quitar" class="text-red-600 text-sm">Cambiar</button>
                    </div>
                @else
                    <input type="text" wire:model.live.debounce.300ms="buscar"
                           placeholder="Escribe al menos 2 letras..."
                           class="w-full border rounded px-3 py-2">
                    @if ($this->resultados->count())
                        <ul class="border rounded mt-1 divide-y bg-white">
                            @foreach ($this->resultados as $r)
                                <li wire:key="res-{{ $r->id }}">
                                    <button type="button" wire:click="seleccionar({{ $r->id }})"
                                            class="w-full text-left px-3 py-2 hover:bg-gray-100">
                                        {{ $r->nombre }}
                                        <span class="text-gray-500 text-sm">(stock {{ rtrim(rtrim(number_format($r->stock, 2, ',', '.'), '0'), ',') }})</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endif
                @error('producto_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold">Tipo</label>
                    <select wire:model.live="tipo" class="w-full border rounded px-3 py-2">
                        <option value="entrada">Entrada (llegó mercancía)</option>
                        <option value="salida">Salida (vencido, dañado, etc.)</option>
                        <option value="ajuste">Ajuste (conteo real)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold">
                        {{ $tipo === 'ajuste' ? 'Cantidad real contada' : 'Cantidad' }}
                    </label>
                    <input type="number" step="0.01" wire:model="cantidad" class="w-full border rounded px-3 py-2">
                    @error('cantidad') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold">Motivo (opcional)</label>
                <input type="text" wire:model="motivo" placeholder="Ej: compra a proveedor, producto vencido..."
                       class="w-full border rounded px-3 py-2">
            </div>

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Registrar movimiento
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-3">Últimos movimientos</h2>
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b font-semibold">
                    <th class="py-2">Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Stock</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->movimientos as $m)
                    <tr class="border-b" wire:key="mov-{{ $m->id }}">
                        <td class="py-2">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $m->producto->nombre }}</td>
                        <td class="{{ $m->tipo === 'entrada' ? 'text-green-700' : ($m->tipo === 'salida' ? 'text-red-600' : 'text-blue-600') }}">
                            {{ ucfirst($m->tipo) }}
                        </td>
                        <td class="text-right">{{ rtrim(rtrim(number_format($m->cantidad, 2, ',', '.'), '0'), ',') }}</td>
                        <td class="text-right">
                            {{ rtrim(rtrim(number_format($m->stock_anterior, 2, ',', '.'), '0'), ',') }}
                            →
                            {{ rtrim(rtrim(number_format($m->stock_nuevo, 2, ',', '.'), '0'), ',') }}
                        </td>
                        <td>{{ $m->motivo }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-4 text-center text-gray-500">Aún no hay movimientos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>