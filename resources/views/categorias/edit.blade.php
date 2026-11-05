<x-layouts.app titulo="Editar categoría">
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Editar categoría</h1>

        <form method="POST" action="{{ route('categorias.update', $categoria) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('categorias._form', ['categoria' => $categoria])
        </form>
    </div>
</x-layouts.app>