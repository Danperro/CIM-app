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
        Schema::create('equipo',function(Blueprint $table){
            $table->id('IdEqo');
            $table->bigInteger('IdLab')->unsigned();
            $table->string('NombreEqo');
            $table->string('CodigoEqo');
            $table->boolean('EstadoEqo');

            $table->foreign('IdLab')->references('IdLab')->on('laboratorio')->onDelete('cascade');
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
