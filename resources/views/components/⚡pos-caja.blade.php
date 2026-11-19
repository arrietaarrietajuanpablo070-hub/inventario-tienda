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
<div class="max-w-6xl mx-auto grid lg:grid-cols-3 gap-6 items-start">
    {{-- Venta en curso --}}
    <div class="lg:col-span-2 bg-white border rounded-lg p-6">
        <h1 class="text-xl font-semibold mb-4">Venta en curso</h1>

        <input
            type="text"
            wire:model.live.debounce.250ms="buscar"
            wire:keydown.enter="entrar"
            placeholder="Nombre del producto o código de barras"
            class="w-full border rounded px-3 py-2.5"
            autofocus
        >

        @if ($this->resultados->count())
            <ul class="border rounded mt-1 divide-y bg-white">
                @foreach ($this->resultados as $r)
                    <li wire:key="res-{{ $r->id }}">
                        <button type="button" wire:click="agregar({{ $r->id }})"
                                class="w-full text-left px-3 py-2 hover:bg-green-50 flex justify-between gap-4">
                            <span>{{ $r->nombre }}</span>
                            <span class="text-gray-500 text-sm whitespace-nowrap">
                                ${{ number_format($r->precio_venta, 0, ',', '.') }} · quedan {{ rtrim(rtrim(number_format($r->stock, 2, ',', '.'), '0'), ',') }}
                            </span>
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif

        @error('carrito') <p class="text-alerta text-sm mt-2">{{ $message }}</p> @enderror

        <table class="w-full text-left mt-4">
            <thead>
                <tr class="border-b">
                    <th class="py-2 font-semibold">Producto</th>
                    <th class="text-right font-semibold">Precio</th>
                    <th class="text-center font-semibold">Cantidad</th>
                    <th class="text-right font-semibold">Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($carrito as $id => $item)
                    <tr class="border-b" wire:key="item-{{ $id }}">
                        <td class="py-2">{{ $item['nombre'] }}</td>
                        <td class="text-right">${{ number_format($item['precio'], 0, ',', '.') }}</td>
                        <td class="text-center whitespace-nowrap">
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
                        <td class="text-right font-semibold">${{ number_format($item['precio'] * $item['cantidad'], 0, ',', '.') }}</td>
                        <td class="text-right">
                            <button type="button" wire:click="quitar({{ $id }})" class="text-alerta hover:underline">Quitar</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-8 text-center text-gray-500">Busca un producto o escanea su código para empezar la venta.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Cobro o comprobante --}}
    <div class="lg:sticky lg:top-6">
        @if ($this->ultimaVenta)
            @php $v = $this->ultimaVenta; @endphp

            <div class="tirilla">
                <div class="text-center">
                    <p class="font-bold text-base">TIENDA LOS PAISAS</p>
                    <p class="text-xs">Venta N.° {{ $v->id }}</p>
                    <p class="text-xs">{{ $v->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <hr class="my-3 border-t border-dashed">

                <div class="space-y-2 text-sm">
                    @foreach ($v->detalles as $d)
                        <div wire:key="det-{{ $d->id }}">
                            <div>{{ $d->producto->nombre }}</div>
                            <div class="flex justify-between">
                                <span>{{ rtrim(rtrim(number_format($d->cantidad, 2, ',', '.'), '0'), ',') }} x ${{ number_format($d->precio_unitario, 0, ',', '.') }}</span>
                                <span>${{ number_format($d->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <hr class="my-3 border-t border-dashed">

                <div class="space-y-1 text-sm">
                    <div class="flex justify-between text-base font-bold"><span>TOTAL</span><span>${{ number_format($v->total, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span>Pago ({{ $v->metodo_pago }})</span><span>${{ number_format($v->pago, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span>Cambio</span><span>${{ number_format($v->cambio, 0, ',', '.') }}</span></div>
                </div>

                <p class="text-center text-xs mt-5">Gracias por su compra</p>
            </div>

            <div class="flex gap-3 mt-4">
                <button type="button" wire:click="nueva" class="flex-1 bg-acento hover:bg-acento-oscuro text-tinta font-semibold px-4 py-3 rounded">Nueva venta</button>
                <button type="button" onclick="window.print()" class="px-4 py-3 rounded border bg-white hover:bg-gray-50">Imprimir</button>
            </div>
        @else
            <div class="bg-white border rounded-lg p-6">
                <p class="text-sm text-gray-500">Total a cobrar</p>
                <p class="text-4xl font-semibold mb-5">${{ number_format($this->total, 0, ',', '.') }}</p>

                <p class="text-sm font-semibold mb-1">Método de pago</p>
                <div class="grid grid-cols-3 gap-2 mb-4">
                    @foreach (['efectivo' => 'Efectivo', 'nequi' => 'Nequi', 'tarjeta' => 'Tarjeta'] as $valor => $texto)
                        <button type="button" wire:click="$set('metodo_pago', '{{ $valor }}')"
                                class="rounded border px-2 py-2 text-sm {{ $metodo_pago === $valor ? 'bg-green-700 border-green-700 text-white font-semibold' : 'bg-white hover:bg-gray-50' }}">
                            {{ $texto }}
                        </button>
                    @endforeach
                </div>

                @if ($metodo_pago === 'efectivo')
                    <label class="block text-sm font-semibold mb-1">Pago del cliente</label>
                    <input type="number" step="50" wire:model.live.debounce.300ms="pago" class="w-full border rounded px-3 py-2">
                    @error('pago') <p class="text-alerta text-sm mt-1">{{ $message }}</p> @enderror

                    <div class="flex flex-wrap gap-2 mt-2">
                        <button type="button" wire:click="$set('pago', {{ round($this->total) }})" class="rounded border px-3 py-1 text-sm hover:bg-gray-50">Exacto</button>
                        @foreach ([10000, 20000, 50000] as $billete)
                            <button type="button" wire:click="$set('pago', {{ $billete }})" class="rounded border px-3 py-1 text-sm hover:bg-gray-50">${{ number_format($billete, 0, ',', '.') }}</button>
                        @endforeach
                    </div>

                    <div class="flex justify-between mt-4 text-lg">
                        <span>Cambio</span>
                        <span class="font-semibold">${{ number_format($this->cambio, 0, ',', '.') }}</span>
                    </div>
                @endif

                <button type="button" wire:click="cobrar" wire:loading.attr="disabled"
                        class="mt-5 w-full bg-acento hover:bg-acento-oscuro text-tinta font-semibold text-lg px-4 py-3 rounded">
                    Cobrar
                </button>
                <button type="button" wire:click="vaciar" class="mt-2 w-full text-sm text-gray-500 hover:underline">Vaciar venta</button>
            </div>
        @endif
    </div>
</div>
