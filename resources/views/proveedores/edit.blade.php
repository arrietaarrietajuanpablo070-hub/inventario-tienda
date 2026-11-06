<x-layouts.app titulo="Editar proveedor">
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Editar proveedor</h1>

        <form method="POST" action="{{ route('proveedores.update', $proveedor) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('proveedores._form', ['proveedor' => $proveedor])
        </form>
    </div>
</x-layouts.app>