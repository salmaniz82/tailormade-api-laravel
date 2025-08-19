<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('swatches', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('productPrice', 50);
            $table->string('imageUrl', 255);
            $table->string('thumbnail', 255)->nullable();
            $table->json('productMeta');
            $table->string('source', 100)->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('trashed')->default(0);
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('swatches');
    }
};
