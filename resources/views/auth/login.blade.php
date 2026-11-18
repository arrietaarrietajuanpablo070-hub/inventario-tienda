<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-sm bg-white rounded-lg shadow p-8">
        <h1 class="text-2xl font-bold text-green-700 text-center">Mi Tienda</h1>
        <p class="text-gray-500 text-center mb-6">Inicia sesión para continuar</p>

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
</body>
</html>