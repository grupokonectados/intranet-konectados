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
            $table->text('query')->nullable();
            $table->text('onlyWhere')->nullable();
            $table->integer('channels')->nullable();
            $table->text('table_name')->nullable();
            $table->text('query_description')->nullable();
            $table->string('prefix_client')->nullable();
            $table->tinyInteger('repeatUsers')->default(0);
            $table->tinyInteger('isActive')->default(0);
            $table->tinyInteger('isDelete')->default(0);
            $table->time('activation_time')->nullable();
            $table->date('activation_date')->nullable();
            $table->tinyInteger('type')->default(0);


            $table->integer('cobertura')->nullable();
            $table->integer('total_registros')->nullable();
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
