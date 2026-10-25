<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('productos', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');
    $table->string('codigo_barras')->unique()->nullable();
    $table->text('descripcion')->nullable();
    $table->decimal('precio_compra', 10, 2)->default(0);
    $table->decimal('precio_venta', 10, 2);
    $table->integer('stock')->default(0);
    $table->integer('stock_minimo')->default(5);
    $table->string('imagen')->nullable();
    $table->boolean('activo')->default(true);
    $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
    $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
