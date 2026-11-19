<x-layouts.app titulo="Nuevo usuario">
    <div class="max-w-xl mx-auto bg-white border rounded-lg p-6">
        <h1 class="text-xl font-semibold mb-4">Nuevo usuario</h1>

        <form method="POST" action="{{ route('usuarios.store') }}" class="space-y-4">
            @csrf
            @include('usuarios._form', ['usuario' => null])
        </form>
    </div>
</x-layouts.app>