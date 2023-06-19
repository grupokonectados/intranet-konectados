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
