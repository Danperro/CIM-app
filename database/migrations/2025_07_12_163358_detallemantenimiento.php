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
        Schema::create('detallemantenimiento', function (Blueprint $table) {
            $table->id('IdDtm');
            $table->bigInteger('IdMan')->unsigned();
            $table->bigInteger('IdEqo')->unsigned();
            $table->date('FechaDtm');
            $table->foreign('IdMan')->references('IdMan')->on('mantenimiento')->onDelete('cascade');
            $table->foreign('IdEqo')->references('IdEqo')->on('equipo')->onDelete('cascade');
            $table->boolean('EstadoDtm');
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
