<div>
    <label class="block text-sm font-semibold">Nombre</label>
    <input type="text" name="nombre" value="{{ old('nombre', $proveedor?->nombre) }}" class="w-full border rounded px-3 py-2">
    @error('nombre') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold">Teléfono (opcional)</label>
        <input type="text" name="telefono" value="{{ old('telefono', $proveedor?->telefono) }}" class="w-full border rounded px-3 py-2">
        @error('telefono') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold">Correo (opcional)</label>
        <input type="email" name="email" value="{{ old('email', $proveedor?->email) }}" class="w-full border rounded px-3 py-2">
        @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold">Dirección (opcional)</label>
    <input type="text" name="direccion" value="{{ old('direccion', $proveedor?->direccion) }}" class="w-full border rounded px-3 py-2">
    @error('direccion') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div class="flex gap-3">
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar</button>
    <a href="{{ route('proveedores.index') }}" class="px-4 py-2 rounded border">Cancelar</a>
</div>