<?php

use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $buscar = '';
    public array $carrito = [];
    public $pago = '';
    public string $metodo_pago = 'efectivo';
    public ?int $ventaId = null;

    #[Computed]
    public function resultados()
    {
        $b = trim($this->buscar);

        if (strlen($b) < 2) {
            return collect();
        }

        return Producto::where('activo', true)
            ->where('stock', '>', 0)
            ->where(fn ($q) => $q->where('nombre', 'like', "%{$b}%")->orWhere('codigo_barras', $b))
            ->orderBy('nombre')
            ->limit(8)
            ->get();
    }

    #[Computed]
    public function total()
    {
        return collect($this->carrito)->sum(fn ($i) => $i['precio'] * $i['cantidad']);
    }

    #[Computed]
    public function cambio()
    {
        if ($this->metodo_pago !== 'efectivo') {
            return 0;
        }

        return max(0, (float) $this->pago - $this->total);
    }

    #[Computed]
    public function ultimaVenta()
    {
        return $this->ventaId ? Venta::with('detalles.producto')->find($this->ventaId) : null;
    }

    public function agregar(int $id): void
    {
        $p = Producto::where('activo', true)->find($id);

        if (!$p || $p->stock <= 0) {
            return;
        }

        $actual = $this->carrito[$id]['cantidad'] ?? 0;

        if ($actual + 1 > (float) $p->stock) {
            $this->addError('carrito', "No hay más stock de {$p->nombre} (disponible: " . (float) $p->stock . ').');
            return;
        }

        $this->resetErrorBag('carrito');

        $this->carrito[$id] = [
            'id' => $p->id,
            'nombre' => $p->nombre,
            'precio' => (float) $p->precio_venta,
            'cantidad' => $actual + 1,
        ];

        $this->buscar = '';
        $this->ventaId = null;
    }

    // Enter en el buscador: agrega por código de barras exacto o si hay un solo resultado
    public function entrar(): void
    {
        $b = trim($this->buscar);

        $p = Producto::where('activo', true)->where('stock', '>', 0)->where('codigo_barras', $b)->first()
            ?? ($this->resultados->count() === 1 ? $this->resultados->first() : null);

        if ($p) {
            $this->agregar($p->id);
        }
    }

    public function actualizar(int $id, $cantidad): void
    {
        if (!isset($this->carrito[$id])) {
            return;
        }

        $cantidad = (float) $cantidad;

        if ($cantidad <= 0) {
            $this->quitar($id);
            return;
        }

        $stock = (float) Producto::whereKey($id)->value('stock');

        if ($cantidad > $stock) {
            $this->addError('carrito', "Solo hay {$stock} disponibles de {$this->carrito[$id]['nombre']}.");
            $cantidad = $stock;
        } else {
            $this->resetErrorBag('carrito');
        }

        $this->carrito[$id]['cantidad'] = $cantidad;
    }

    public function quitar(int $id): void
    {
        unset($this->carrito[$id]);
    }

    public function vaciar(): void
    {
        $this->reset(['carrito', 'pago', 'buscar']);
        $this->resetErrorBag();
    }

    public function nueva(): void
    {
        $this->ventaId = null;
    }

    public function cobrar(): void
    {
        $this->resetErrorBag();

        if (empty($this->carrito)) {
            $this->addError('carrito', 'Agrega al menos un producto.');
            return;
        }

        $this->validate(['metodo_pago' => 'required|in:efectivo,nequi,tarjeta']);

        $venta = DB::transaction(function () {
            $total = 0;
            $lineas = [];

            // Los precios y el stock se leen de la base de datos, no del carrito.
            foreach ($this->carrito as $id => $item) {
                $p = Producto::lockForUpdate()->findOrFail($id);
                $cant = (float) $item['cantidad'];

                if (!$p->activo || $cant > (float) $p->stock) {
                    throw ValidationException::withMessages([
                        'carrito' => "Stock insuficiente de {$p->nombre} (disponible: " . (float) $p->stock . ').',
                    ]);
                }

                $sub = round($cant * (float) $p->precio_venta, 2);
                $total += $sub;
                $lineas[] = [$p, $cant, $sub];
            }

            $pago = $this->metodo_pago === 'efectivo' ? (float) $this->pago : $total;

            if ($pago < $total) {
                throw ValidationException::withMessages([
                    'pago' => 'El pago es menor que el total ($' . number_format($total, 0, ',', '.') . ').',
                ]);
            }

            $venta = Venta::create([
                'user_id' => auth()->id(),
                'total' => $total,
                'pago' => $pago,
                'cambio' => $pago - $total,
                'metodo_pago' => $this->metodo_pago,
                'canal' => 'local',
                'estado' => 'completada',
            ]);

            foreach ($lineas as [$p, $cant, $sub]) {
                $venta->detalles()->create([
                    'producto_id' => $p->id,
                    'cantidad' => $cant,
                    'precio_unitario' => $p->precio_venta,
                    'subtotal' => $sub,
                ]);

                $antes = (float) $p->stock;
                $nuevo = $antes - $cant;
                $p->update(['stock' => $nuevo]);

                MovimientoInventario::create([
                    'producto_id' => $p->id,
                    'user_id' => auth()->id(),
                    'tipo' => 'salida',
                    'cantidad' => $cant,
                    'stock_anterior' => $antes,
                    'stock_nuevo' => $nuevo,
                    'motivo' => 'Venta #' . $venta->id,
                ]);
            }

            return $venta;
        });

        $this->ventaId = $venta->id;
        $this->reset(['carrito', 'pago', 'buscar']);
        $this->metodo_pago = 'efectivo';
    }
};
?>

<div class="max-w-6xl mx-auto grid md:grid-cols-5 gap-6">
    {{-- Columna izquierda: buscador y carrito --}}
    <div class="md:col-span-3 bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Caja</h1>

        <input
            type="text"
            wire:model.live.debounce.250ms="buscar"
            wire:keydown.enter="entrar"
            placeholder="Busca por nombre o escanea el código de barras..."
            class="w-full border rounded px-3 py-2"
            autofocus
        >

        @if ($this->resultados->count())
            <ul class="border rounded mt-1 divide-y bg-white">
                @foreach ($this->resultados as $r)
                    <li wire:key="res-{{ $r->id }}">
                        <button type="button" wire:click="agregar({{ $r->id }})"
                                class="w-full text-left px-3 py-2 hover:bg-gray-100 flex justify-between">
                            <span>{{ $r->nombre }}</span>
                            <span class="text-gray-500 text-sm">
                                ${{ number_format($r->precio_venta, 0, ',', '.') }} · stock {{ rtrim(rtrim(number_format($r->stock, 2, ',', '.'), '0'), ',') }}
                            </span>
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif

        @error('carrito') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror

        <table class="w-full text-left mt-4">
            <thead>
                <tr class="border-b font-semibold">
                    <th class="py-2">Producto</th>
                    <th class="text-right">Precio</th>
                    <th class="text-right">Cant.</th>
                    <th class="text-right">Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($carrito as $id => $item)
                    <tr class="border-b" wire:key="item-{{ $id }}">
                        <td class="py-2">{{ $item['nombre'] }}</td>
                        <td class="text-right">${{ number_format($item['precio'], 0, ',', '.') }}</td>
                                                <td class="text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1">
                                <button type="button"
                                        wire:click="actualizar({{ $id }}, {{ $item['cantidad'] - 1 }})"
                                        class="w-8 h-8 border rounded hover:bg-gray-100">−</button>
                                <input type="number" step="any" min="0" value="{{ $item['cantidad'] }}"
                                       wire:change="actualizar({{ $id }}, $event.target.value)"
                                       class="w-16 border rounded px-2 py-1 text-center [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                                <button type="button"
                                        wire:click="actualizar({{ $id }}, {{ $item['cantidad'] + 1 }})"
                                        class="w-8 h-8 border rounded hover:bg-gray-100">+</button>
                            </div>
                        </td>
                        <td class="text-right">${{ number_format($item['precio'] * $item['cantidad'], 0, ',', '.') }}</td>
                        <td class="text-right">
                            <button type="button" wire:click="quitar({{ $id }})" class="text-red-600 hover:underline">Quitar</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-gray-500">El carrito está vacío. Busca un producto para empezar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Columna derecha: cobro o comprobante --}}
    <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
        @if ($this->ultimaVenta)
            <div class="bg-green-50 border border-green-200 rounded p-4">
                <h2 class="text-xl font-bold text-green-800">Venta #{{ $this->ultimaVenta->id }} registrada</h2>
                <p class="text-sm text-gray-600 mb-3">{{ $this->ultimaVenta->created_at->format('d/m/Y H:i') }}</p>

                <table class="w-full text-sm mb-3">
                    @foreach ($this->ultimaVenta->detalles as $d)
                        <tr wire:key="det-{{ $d->id }}">
                            <td class="py-1">
                                {{ rtrim(rtrim(number_format($d->cantidad, 2, ',', '.'), '0'), ',') }} × {{ $d->producto->nombre }}
                            </td>
                            <td class="text-right">${{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </table>

                <div class="border-t pt-2 space-y-1">
                    <div class="flex justify-between font-bold"><span>Total</span><span>${{ number_format($this->ultimaVenta->total, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span>Pago ({{ $this->ultimaVenta->metodo_pago }})</span><span>${{ number_format($this->ultimaVenta->pago, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span>Cambio</span><span>${{ number_format($this->ultimaVenta->cambio, 0, ',', '.') }}</span></div>
                </div>
            </div>

            <div class="flex gap-3 mt-4">
                <button type="button" wire:click="nueva" class="flex-1 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Nueva venta</button>
                <button type="button" onclick="window.print()" class="px-4 py-2 rounded border">Imprimir</button>
            </div>
        @else
            <h2 class="text-xl font-bold mb-3">Cobro</h2>

            <div class="flex justify-between text-2xl font-bold mb-4">
                <span>Total</span>
                <span>${{ number_format($this->total, 0, ',', '.') }}</span>
            </div>

            <label class="block text-sm font-semibold">Método de pago</label>
            <select wire:model.live="metodo_pago" class="w-full border rounded px-3 py-2 mb-3">
                <option value="efectivo">Efectivo</option>
                <option value="nequi">Nequi</option>
                <option value="tarjeta">Tarjeta</option>
            </select>

            @if ($metodo_pago === 'efectivo')
                <label class="block text-sm font-semibold">Pago del cliente</label>
                <input type="number" step="50" wire:model.live.debounce.300ms="pago" class="w-full border rounded px-3 py-2">
                @error('pago') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

                <div class="flex justify-between mt-3 text-lg">
                    <span>Cambio</span>
                    <span class="font-bold">${{ number_format($this->cambio, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="flex gap-3 mt-5">
                <button type="button" wire:click="cobrar" wire:loading.attr="disabled"
                        class="flex-1 bg-green-600 text-white px-4 py-3 rounded hover:bg-green-700 font-semibold">
                    Cobrar
                </button>
                <button type="button" wire:click="vaciar" class="px-4 py-3 rounded border">Vaciar</button>
            </div>
        @endif
    </div>
</div>