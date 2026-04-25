<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCategoriaToProductosVendidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productos_vendidos', function (Blueprint $table) {
            $table->string('categoria')->default('General')->after('descripcion');
        });

        $productosVendidos = DB::table('productos_vendidos')->get(['id', 'codigo_barras']);
        foreach ($productosVendidos as $productoVendido) {
            $categoria = DB::table('productos')
                ->where('codigo_barras', $productoVendido->codigo_barras)
                ->value('categoria') ?: 'General';

            DB::table('productos_vendidos')
                ->where('id', $productoVendido->id)
                ->update(['categoria' => $categoria]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productos_vendidos', function (Blueprint $table) {
            $table->dropColumn('categoria');
        });
    }
}