<x-layouts.app titulo="Inventario">
    <div class="max-w-5xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Productos</p>
            <p class="text-2xl font-bold">{{ number_format($resumen['productos'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Categorías</p>
            <p class="text-2xl font-bold">{{ number_format($resumen['categorias'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Proveedores</p>
            <p class="text-2xl font-bold">{{ number_format($resumen['proveedores'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Valor del inventario</p>
            <p class="text-2xl font-bold">${{ number_format($resumen['valor'], 0, ',', '.') }}</p>
        </div>
    </div>

    <livewire:productos-tabla />
</x-layouts.app>