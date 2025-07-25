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
        Schema::create('laboratorio',function(Blueprint $table){
            $table->id('IdLab');
            $table->bigInteger('IdAre')->unsigned();
            $table->string('NombreLab');
            $table->boolean('EstadoLab');

            $table->foreign('IdAre')->references('IdAre')->on('area')->onDelete('cascade');
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
