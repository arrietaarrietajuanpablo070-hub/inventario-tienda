<x-layouts.app titulo="Editar producto">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Editar producto</h1>

        <form method="POST" action="{{ route('productos.update', $producto) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('productos._form')
        </form>
    </div>
</x-layouts.app>