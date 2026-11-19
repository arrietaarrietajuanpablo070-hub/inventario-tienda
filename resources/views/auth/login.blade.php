<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · {{ config('app.name') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 100 100%27><text y=%27.9em%27 font-size=%2790%27>🛒</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-green-700 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-sm">
        <div class="text-center text-white mb-6">
            <div class="text-6xl mb-2">🛒</div>
            <h1 class="text-3xl font-bold">{{ config('app.name') }}</h1>
            <p class="text-green-100">Inventario, caja y ventas</p>
        </div>

        <div class="bg-white rounded-xl shadow-xl p-8">
            <h2 class="text-xl font-bold mb-4">Iniciar sesión</h2>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold">Correo</label>
                    <input type="email" name="email" value="{{ old('email') }}" autofocus class="w-full border rounded px-3 py-2">
                    @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold">Contraseña</label>
                    <input type="password" name="password" class="w-full border rounded px-3 py-2">
                    @error('password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="recordar" value="1">
                    Recordarme
                </label>

                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 font-semibold">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>