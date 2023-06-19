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
        Schema::create('cartera_primer_dia', function (Blueprint $table) {
            $table->id();
            $table->integer('ic')->nullable();
            $table->string('rut')->nullable();
            $table->string('cod_cartera')->nullable();
            $table->integer('cc')->nullable();
            $table->string('nombre')->nullable();
            $table->string('direccion_contractual')->nullable();
            $table->string('comuna')->nullable();
            $table->string('ciudad')->nullable();
            $table->integer('region')->nullable();
            $table->string('direccion_facturacion')->nullable();
            $table->string('comuna_f')->nullable();
            $table->string('ciudad_f')->nullable();
            $table->string('region_f')->nullable();
            $table->string('fijo1')->nullable();
            $table->string('fijo2')->nullable();
            $table->string('fijo3')->nullable();
            $table->string('movil1')->nullable();
            $table->string('movil2')->nullable();
            $table->string('movil3')->nullable();
            $table->string('email1')->nullable();
            $table->string('email2')->nullable();
            $table->string('email3')->nullable();
            $table->string('monto')->nullable();
            $table->string('contador')->nullable();
            $table->string('tipo_cobranza')->nullable();
            $table->string('dias')->nullable();
            $table->string('cesion')->nullable();
            $table->string('honorario')->nullable();
            $table->string('vigente')->nullable();
            $table->string('inhabilita')->nullable();
            $table->string('ptt')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_a_c_s_a_tables');
    }
};
