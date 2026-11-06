<x-layouts.app titulo="Nuevo proveedor">
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Nuevo proveedor</h1>

        <form method="POST" action="{{ route('proveedores.store') }}" class="space-y-4">
            @csrf
            @include('proveedores._form', ['proveedor' => null])
        </form>
    </div>
</x-layouts.app>