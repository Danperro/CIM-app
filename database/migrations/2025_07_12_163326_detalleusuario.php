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
        Schema::create('detalleusuario',function(Blueprint $table){
            $table->id('IdDtu');
            $table->bigInteger('IdUsa')->unsigned();
            $table->bigInteger('IdLab')->unsigned();
            $table->boolean('EstadoDtu');
            $table->foreign('IdUsa')->references('IdUsa')->on('usuario')->onDelete('cascade');
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
