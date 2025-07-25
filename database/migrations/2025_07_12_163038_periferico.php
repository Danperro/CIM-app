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
        Schema::create('periferico',function(Blueprint $table){
            $table->id('IdPef');
            $table->bigInteger('IdTpf')->unsigned();
            $table->string('CiuPef');
            $table->string('CodigoInventarioPef');
            $table->string('MarcaPef');
            $table->string('ColorPef');
            $table->boolean('EstadoPef');
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
