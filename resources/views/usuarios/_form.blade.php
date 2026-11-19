<div>
    <label class="block text-sm font-semibold mb-1">Nombre</label>
    <input type="text" name="name" value="{{ old('name', $usuario?->name) }}" class="w-full border rounded px-3 py-2">
    @error('name') <p class="text-alerta text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-semibold mb-1">Correo</label>
    <input type="email" name="email" value="{{ old('email', $usuario?->email) }}" class="w-full border rounded px-3 py-2">
    @error('email') <p class="text-alerta text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-semibold mb-1">Rol</label>
    <select name="rol" class="w-full border rounded px-3 py-2">
        @foreach (['admin' => 'Administrador', 'cajero' => 'Cajero', 'cliente' => 'Cliente'] as $valor => $texto)
            <option value="{{ $valor }}" @selected(old('rol', $usuario?->rol ?? 'cajero') === $valor)>{{ $texto }}</option>
        @endforeach
    </select>
    @error('rol') <p class="text-alerta text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-semibold mb-1">Contraseña</label>
    <input type="password" name="password" class="w-full border rounded px-3 py-2">
    <p class="text-xs text-gray-500 mt-1">
        {{ $usuario ? 'Déjala vacía si no quieres cambiarla.' : 'Mínimo 8 caracteres.' }}
    </p>
    @error('password') <p class="text-alerta text-sm mt-1">{{ $message }}</p> @enderror
</div>

<div class="flex gap-3">
    <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">Guardar</button>
    <a href="{{ route('usuarios.index') }}" class="px-4 py-2 rounded border bg-white">Cancelar</a>
</div>