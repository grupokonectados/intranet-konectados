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
        Schema::create('estructuras', function (Blueprint $table) {
            $table->id();
            $table->string('TABLE_CATALOG')->nullable();
            $table->string('PREFIX')->nullable();
            $table->string('TABLE_NAME')->nullable();
            $table->string('COLUMN_NAME')->nullable();
            $table->integer('ORDINAL_POSITION')->nullable();
            $table->string('COLUMN_DEFAULT')->nullable();
            $table->string('IS_NULLABLE')->nullable();
            $table->string('DATA_TYPE')->nullable();
            $table->string('CHARACTER_MAXIMUM_LENGTH')->nullable()->default(0);
            $table->string('CHARACTER_OCTET_LENGTH')->nullable()->default(0);
            $table->string('NUMERIC_PRECISION')->nullable()->default(0);
            $table->string('NUMERIC_SCALE')->nullable()->default(0);
            $table->string('DATETIME_PRECISION')->nullable();
            $table->string('CHARACTER_SET_NAME')->nullable();
            $table->string('COLLATION_NAME')->nullable();
            $table->string('COLUMN_TYPE')->nullable();
            $table->string('COLUMN_KEY')->nullable();
            $table->string('EXTRA')->nullable();
            $table->string('PRIVILEGES')->nullable();
            $table->string('COLUMN_COMMENT')->nullable();
            $table->string('GENERATION_EXPRESSION')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estructuras');
    }
};
