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
        Schema::create('estrategias', function (Blueprint $table) {
            $table->id();
            $table->string('prefix_client');
            $table->integer('channels');
            $table->string('table_name');
            $table->string('onlyWhere');
            $table->tinyInteger('repeatUsers')->default(0);
            $table->date('activation_date')->nullable();
            $table->time('activation_time')->nullable();
            $table->integer('registros_unicos')->nullable();
            $table->integer('registros_repetidos')->nullable();
            $table->integer('total_registros')->nullable();
            $table->double('cobertura', 8, 2)->nullable();
            $table->json('registros')->nullable();
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('isActive')->default(0);
            $table->tinyInteger('isDelete')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estrategias');
    }
};
