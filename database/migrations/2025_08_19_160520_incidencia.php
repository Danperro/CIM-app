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
        Schema::create('incidencia',function(Blueprint $table){
            $table->id('IdInc');
            $table->bigInteger('IdMan')->unsigned();
            $table->bigInteger('IdTpf')->unsigned();
            $table->string('NombreInc');
            $table->boolean('EstadoInc');

            $table->foreign('IdMan')->references('IdMan')->on('mantenimiento')->onDelete('cascade');
            $table->foreign('IdTpf')->references('IdTpf')->on('tipoperiferico')->onDelete('cascade');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
