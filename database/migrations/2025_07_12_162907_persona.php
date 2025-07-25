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
        //
        Schema::create('persona',function(Blueprint $table){
            $table->Id('IdPer');
            $table->string('NombrePer');
            $table->string('ApellidoPaternoPer');
            $table->string('ApellidoMaternoPer');
            $table->date('FechaNacimientoPer');
            $table->string('DniPer');
            $table->string('TelefonoPer');
            $table->string('CorreoPer');
            $table->boolean('EstadoPer');
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
