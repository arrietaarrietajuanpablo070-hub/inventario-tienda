<x-layouts.app titulo="Productos">
    <div class="max-w-5xl mx-auto mb-6 bg-white border rounded-lg grid grid-cols-2 md:grid-cols-4 divide-x divide-y md:divide-y-0">
        <div class="p-4">
            <p class="text-sm text-gray-500">Productos</p>
            <p class="text-2xl font-semibold">{{ number_format($resumen['productos'], 0, ',', '.') }}</p>
        </div>
        <div class="p-4">
            <p class="text-sm text-gray-500">Categorías</p>
            <p class="text-2xl font-semibold">{{ number_format($resumen['categorias'], 0, ',', '.') }}</p>
        </div>
        <div class="p-4">
            <p class="text-sm text-gray-500">Proveedores</p>
            <p class="text-2xl font-semibold">{{ number_format($resumen['proveedores'], 0, ',', '.') }}</p>
        </div>
        <div class="p-4">
            <p class="text-sm text-gray-500">Valor del inventario</p>
            <p class="text-2xl font-semibold">${{ number_format($resumen['valor'], 0, ',', '.') }}</p>
        </div>
    </div>

    <livewire:productos-tabla />
</x-layouts.app>