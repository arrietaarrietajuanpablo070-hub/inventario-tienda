<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta · {{ config('app.name') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%231e4d3b'/><text x='16' y='22' font-size='15' font-weight='700' text-anchor='middle' fill='%23e8b21a' font-family='sans-serif'>LP</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 antialiased">
    <div class="min-h-screen md:grid md:grid-cols-2">
        <div class="bg-green-700 text-white p-8 md:p-14 flex flex-col justify-end">
            <p class="text-green-200">Tienda</p>
            <h1 class="text-5xl font-bold leading-none mb-4">Los Paisas</h1>
            <p class="text-green-100 max-w-xs">Crea tu cuenta y mira los productos disponibles antes de venir a la tienda.</p>
        </div>

        <div class="flex items-center justify-center p-8">
            <form method="POST" action="{{ route('registro') }}" class="w-full max-w-sm space-y-4">
                @csrf
                <h2 class="text-2xl font-semibold">Crear cuenta</h2>

                <div>
                    <label class="block text-sm font-semibold mb-1">Nombre</label>
                    <input type="text" name="name" value="{{ old('name') }}" autofocus class="w-full border rounded px-3 py-2 bg-white">
                    @error('name') <p class="text-alerta text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Correo</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2 bg-white">
                    @error('email') <p class="text-alerta text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Contraseña</label>
                    <input type="password" name="password" class="w-full border rounded px-3 py-2 bg-white">
                    <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres.</p>
                    @error('password') <p class="text-alerta text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Repite la contraseña</label>
                    <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2 bg-white">
                </div>

                <button type="submit" class="w-full bg-green-700 text-white px-4 py-2.5 rounded hover:bg-green-800 font-semibold">Crear cuenta</button>

                <p class="text-sm text-center">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-green-700 font-semibold hover:underline">Entrar</a></p>
            </form>
        </div>
    </div>
</body>
</html>