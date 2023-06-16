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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('prefix')->nullable();
            $table->tinyInteger('manual')->nullable();
            $table->tinyInteger('allcontacts')->nullable();
            $table->tinyInteger('lists')->nullable();
            $table->tinyInteger('gestion_ejecutivo')->nullable();
            $table->string('destinatarios')->nullable();
            $table->tinyInteger('active')->nullable();
            $table->tinyInteger('idempresa')->nullable();
            $table->tinyInteger('stat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
