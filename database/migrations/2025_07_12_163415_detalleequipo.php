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
        Schema::create('detalleequipo',function(Blueprint $table){
            $table->id('IdDte');
            $table->bigInteger('IdEqo')->unsigned();
            $table->bigInteger('IdPef')->unsigned();
            $table->boolean('EstadoDte');
            $table->foreign('IdEqo')->references('IdEqo')->on('equipo')->onDelete('cascade');
            $table->foreign('IdPef')->references('IdPef')->on('periferico')->onDelete('cascade');
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
