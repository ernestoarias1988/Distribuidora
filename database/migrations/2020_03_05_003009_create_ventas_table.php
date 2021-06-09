<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //date_default_timezone_set('America/Argentina/Salta');
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->integer('entregado')->default('0');
            $table->string('pagado')->default('0');
            $table->string('vendedor');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas');
    }
}
