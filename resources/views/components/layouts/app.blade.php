<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo ?? config('app.name') }} · {{ config('app.name') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 100 100%27><text y=%27.9em%27 font-size=%2790%27>🛒</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    @php
        $rol = auth()->user()?->rol;
        $enlace = fn ($patron) => 'px-3 py-1 rounded hover:bg-green-600 '
            . (request()->is($patron) ? 'bg-green-800 font-semibold' : '');
    @endphp

    <nav class="bg-green-700 text-white shadow sticky top-0 z-10 print:hidden">
        <div class="max-w-5xl mx-auto px-6 py-3 flex flex-wrap items-center gap-x-2 gap-y-2">
            <a href="{{ route('pos.index') }}" class="flex items-center gap-2 font-bold text-lg mr-4">
                <span class="text-2xl">🛒</span>{{ config('app.name') }}
            </a>

            <a href="{{ route('pos.index') }}" class="{{ $enlace('pos*') }}">Caja</a>

            @if ($rol === 'admin')
                <a href="{{ route('productos.index') }}" class="{{ $enlace('productos*') }}">Inventario</a>
                <a href="{{ route('categorias.index') }}" class="{{ $enlace('categorias*') }}">Categorías</a>
                <a href="{{ route('proveedores.index') }}" class="{{ $enlace('proveedores*') }}">Proveedores</a>
                <a href="{{ route('movimientos.index') }}" class="{{ $enlace('movimientos*') }}">Movimientos</a>
            @endif

            <a href="{{ route('ventas.index') }}" class="{{ $enlace('ventas*') }}">Ventas</a>

            @if ($rol === 'admin')
                <a href="{{ route('reportes.index') }}" class="{{ $enlace('reportes*') }}">Reportes</a>
            @endif

            <div class="ml-auto flex items-center gap-3 text-sm">
                <span class="bg-green-800 rounded-full px-3 py-1">{{ auth()->user()?->name }} · {{ $rol }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="underline hover:text-green-100">Salir</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="p-6">
        {{ $slot }}
    </main>

    <script>
        // Los mensajes de éxito se ocultan solos a los 5 segundos.
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('main div[class*="bg-green-100"]').forEach((el) => {
                setTimeout(() => {
                    el.style.transition = 'opacity .5s';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>