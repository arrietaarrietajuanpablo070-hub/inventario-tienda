<x-layouts.app titulo="Editar usuario">
    <div class="max-w-xl mx-auto bg-white border rounded-lg p-6">
        <h1 class="text-xl font-semibold mb-4">Editar usuario</h1>

        <form method="POST" action="{{ route('usuarios.update', $usuario) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('usuarios._form', ['usuario' => $usuario])
        </form>
    </div>
</x-layouts.app>