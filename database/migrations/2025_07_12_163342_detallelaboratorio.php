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
        Schema::create('detallelaboratorio',function(Blueprint $table){
            $table->id('IdDtl');
            $table->bigInteger('IdLab')->unsigned();
            $table->string('ObservaciónDtl');
            $table->string('RealizadoDtl');
            $table->string('VerificadoDtl');
            $table->date('FechaDtl');
            $table->boolean('EstadoDtl');
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
