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
        Schema::create('incidenciaperiferico', function (Blueprint $table) {
            $table->id('IdIpf');
            $table->bigInteger('IdInc')->unsigned();
            $table->bigInteger('IdPef')->unsigned();
            $table->date('FechaIpf');
            $table->boolean('EstadoIpf');
            $table->foreign('IdInc')->references('IdInc')->on('incidencia')->onDelete('cascade');
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
