<x-layouts.app titulo="Nueva categoría">
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Nueva categoría</h1>

        <form method="POST" action="{{ route('categorias.store') }}" class="space-y-4">
            @csrf
            @include('categorias._form', ['categoria' => null])
        </form>
    </div>
</x-layouts.app>