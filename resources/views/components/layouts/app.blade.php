<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo ?? 'Mi Tienda' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <nav class="bg-green-700 text-white">
        <div class="max-w-5xl mx-auto px-6 py-3 flex items-center gap-6">
            <span class="font-bold text-lg">Mi Tienda</span>
            <a href="{{ route('productos.index') }}" class="hover:underline {{ request()->is('productos*') ? 'underline font-semibold' : '' }}">Inventario</a>
            <a href="{{ route('categorias.index') }}" class="hover:underline {{ request()->is('categorias*') ? 'underline font-semibold' : '' }}">Categorías</a>
            <a href="{{ route('movimientos.index') }}" class="hover:underline {{ request()->is('movimientos*') ? 'underline font-semibold' : '' }}">Movimientos</a>
        </div>
    </nav>

    <main class="p-6">
        {{ $slot }}
    </main>
</body>
</html>