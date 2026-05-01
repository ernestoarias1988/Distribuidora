<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        DB::table('categorias')->insert([
            ['nombre' => 'General', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $categoriasExistentes = DB::table('productos')
            ->select('categoria')
            ->distinct()
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->pluck('categoria');

        foreach ($categoriasExistentes as $categoria) {
            DB::table('categorias')->updateOrInsert(
                ['nombre' => $categoria],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categorias');
    }
}