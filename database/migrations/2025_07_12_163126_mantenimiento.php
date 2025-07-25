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
        Schema::create('mantenimiento',function(Blueprint $table){
            $table->id('IdMan');
            $table->bigInteger('IdTpm')->unsigned();
            $table->bigInteger('IdClm')->unsigned();
            $table->string('NombreMan');
            $table->boolean('EstadoMan');

            $table->foreign('IdTpm')->references('IdTpm')->on('tipomantenimiento')->onDelete('cascade');
            $table->foreign('IdClm')->references('IdClm')->on('clasemantenimiento')->onDelete('cascade');
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
