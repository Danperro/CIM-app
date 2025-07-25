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
        Schema::create('usuario',function(Blueprint $table){
            $table->id('IdUsa');
            $table->bigInteger('IdRol')->unsigned();
            $table->bigInteger('IdPer')->unsigned();
            $table->string('UsernameUsa');
            $table->string('PasswordUsa');
            $table->boolean('EstadoUsa');

            $table->foreign('IdRol')->references('IdRol')->on('rol')->onDelete('cascade');
            $table->foreign('IdPer')->references('IdPer')->on('persona')->onDelete('cascade');
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
