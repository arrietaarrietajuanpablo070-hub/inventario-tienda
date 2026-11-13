<div>
    <label class="block text-sm font-semibold">Nombre</label>
    <input type="text" name="nombre" value="{{ old('nombre', $producto?->nombre) }}" class="w-full border rounded px-3 py-2">
    @error('nombre') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold">Código de barras (opcional)</label>
        <input type="text" name="codigo_barras" value="{{ old('codigo_barras', $producto?->codigo_barras) }}" class="w-full border rounded px-3 py-2">
        @error('codigo_barras') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold">Categoría</label>
        <select name="categoria_id" class="w-full border rounded px-3 py-2">
            <option value="">Selecciona una...</option>
            @foreach ($categorias as $c)
                <option value="{{ $c->id }}" @selected(old('categoria_id', $producto?->categoria_id) == $c->id)>{{ $c->nombre }}</option>
            @endforeach
        </select>
        @error('categoria_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold">Proveedor (opcional)</label>
    <select name="proveedor_id" class="w-full border rounded px-3 py-2">
        <option value="">Sin proveedor</option>
        @foreach ($proveedores as $p)
            <option value="{{ $p->id }}" @selected(old('proveedor_id', $producto?->proveedor_id) == $p->id)>{{ $p->nombre }}</option>
        @endforeach
    </select>
    @error('proveedor_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-semibold">Descripción (opcional)</label>
    <textarea name="descripcion" rows="2" class="w-full border rounded px-3 py-2">{{ old('descripcion', $producto?->descripcion) }}</textarea>
    @error('descripcion') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold">Precio de compra</label>
        <input type="number" step="0.01" name="precio_compra" value="{{ old('precio_compra', $producto?->precio_compra ?? 0) }}" class="w-full border rounded px-3 py-2">
        @error('precio_compra') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold">Precio de venta</label>
        <input type="number" step="0.01" name="precio_venta" value="{{ old('precio_venta', $producto?->precio_venta ?? 0) }}" class="w-full border rounded px-3 py-2">
        @error('precio_venta') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold">Stock</label>
        <input type="number" step="0.01" name="stock" value="{{ old('stock', $producto?->stock ?? 0) }}" class="w-full border rounded px-3 py-2">
        @error('stock') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold">Stock mínimo</label>
        <input type="number" name="stock_minimo" value="{{ old('stock_minimo', $producto?->stock_minimo ?? 5) }}" class="w-full border rounded px-3 py-2">
        @error('stock_minimo') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <input type="hidden" name="activo" value="0">
    <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="activo" value="1" @checked(old('activo', $producto?->activo ?? true))>
        <span class="text-sm font-semibold">Producto activo (disponible para la venta)</span>
    </label>
</div>

<div class="flex gap-3">
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar</button>
    <a href="{{ route('productos.index') }}" class="px-4 py-2 rounded border">Cancelar</a>
</div>