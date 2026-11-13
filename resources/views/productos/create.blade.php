<x-layouts.app titulo="Nuevo producto">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Nuevo producto</h1>

        <form method="POST" action="{{ route('productos.store') }}" class="space-y-4">
            @csrf
            @include('productos._form')
        </form>
    </div>
</x-layouts.app>