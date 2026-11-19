<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo ?? 'Inicio' }} · {{ config('app.name') }}</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%231e4d3b'/><text x='16' y='22' font-size='15' font-weight='700' text-anchor='middle' fill='%23e8b21a' font-family='sans-serif'>LP</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 antialiased">
    @php
        $rol = auth()->user()?->rol;
        $enlace = fn ($patron) => 'block rounded px-3 py-2 text-sm whitespace-nowrap hover:bg-green-800 '
            . (request()->is($patron) ? 'bg-green-800 font-semibold text-white' : 'text-green-100');
        $grupo = 'hidden md:block px-3 pt-5 pb-1 text-xs text-green-200';
    @endphp

    <div class="md:flex md:min-h-screen">
        <aside class="bg-green-700 text-white md:w-60 md:shrink-0 md:sticky md:top-0 md:h-screen md:flex md:flex-col print:hidden">
            <a href="{{ route('pos.index') }}" class="block px-5 pt-5 pb-3">
                <span class="block text-sm text-green-200 leading-none">Tienda</span>
                <span class="block text-xl font-bold leading-tight">Los Paisas</span>
            </a>

            <nav class="flex md:block gap-1 overflow-x-auto px-3 pb-3 md:pb-0 md:flex-1 md:overflow-y-auto">
                <p class="{{ $grupo }}">Venta</p>
                <a href="{{ route('pos.index') }}" class="{{ $enlace('pos*') }}">Caja</a>
                <a href="{{ route('ventas.index') }}" class="{{ $enlace('ventas*') }}">Ventas</a>

                @if ($rol === 'admin')
                    <p class="{{ $grupo }}">Almacén</p>
                    <a href="{{ route('productos.index') }}" class="{{ $enlace('productos*') }}">Productos</a>
                    <a href="{{ route('categorias.index') }}" class="{{ $enlace('categorias*') }}">Categorías</a>
                    <a href="{{ route('proveedores.index') }}" class="{{ $enlace('proveedores*') }}">Proveedores</a>
                    <a href="{{ route('movimientos.index') }}" class="{{ $enlace('movimientos*') }}">Movimientos</a>

                    <p class="{{ $grupo }}">Análisis</p>
                    <a href="{{ route('reportes.index') }}" class="{{ $enlace('reportes*') }}">Reportes</a>
                @endif
            </nav>

            <div class="flex items-center justify-between gap-3 border-t border-green-800 px-5 py-3 text-sm">
                <div class="leading-tight">
                    <p class="font-semibold">{{ auth()->user()?->name }}</p>
                    <p class="text-green-200 text-xs">{{ $rol === 'admin' ? 'Administrador' : 'Cajero' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="underline text-green-100 hover:text-white">Salir</button>
                </form>
            </div>
        </aside>

        <main class="flex-1 min-w-0 p-6 md:p-8">
            {{ $slot }}
        </main>
    </div>

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