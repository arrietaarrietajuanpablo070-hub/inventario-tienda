<div>
    <label class="block text-sm font-semibold">Nombre</label>
    <input type="text" name="nombre" value="{{ old('nombre', $categoria?->nombre) }}" class="w-full border rounded px-3 py-2">
    @error('nombre') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-semibold">Descripción (opcional)</label>
    <textarea name="descripcion" rows="3" class="w-full border rounded px-3 py-2">{{ old('descripcion', $categoria?->descripcion) }}</textarea>
    @error('descripcion') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div class="flex gap-3">
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Guardar</button>
    <a href="{{ route('categorias.index') }}" class="px-4 py-2 rounded border">Cancelar</a>
</div>