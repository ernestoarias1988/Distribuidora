<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendedorToClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // La columna se usa desde 2022 pero nunca tuvo migracion; en algunas bases ya existe
        if (!Schema::hasColumn('clientes', 'vendedor')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->string('vendedor')->nullable()->after('lista');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('clientes', 'vendedor')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropColumn('vendedor');
            });
        }
    }
}
