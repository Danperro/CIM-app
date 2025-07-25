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
        Schema::create('acceso',function(Blueprint $table){
            $table->id('IdAcs');
            $table->bigInteger('IdRol')->unsigned();
            $table->bigInteger('IdPem')->unsigned();
            $table->bigInteger('IdMen')->unsigned();

            $table->foreign('IdRol')->references('IdRol')->on('rol')->onDelete('cascade');
            $table->foreign('IdPem')->references('IdPem')->on('permiso')->onDelete('cascade');
            $table->foreign('IdMen')->references('IdMen')->on('menu')->onDelete('cascade');
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
