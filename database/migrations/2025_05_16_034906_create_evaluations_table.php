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
       Schema::create('Evaluations', function (Blueprint $table) {
    $table->id();
    $table->integer('training_percentage'); // 10, 20, ..., 100
    $table->integer('k_value')->nullable(); // nilai K yang digunakan
    $table->float('accuracy')->nullable();
    $table->float('error_rate')->nullable();
    $table->json('confusion_matrix')->nullable(); // simpan TP, FP, TN, FN dalam JSON
    $table->float('precision')->nullable();
    $table->float('recall')->nullable();
    $table->float('f1_score')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
