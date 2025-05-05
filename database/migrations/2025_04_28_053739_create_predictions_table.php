<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_predictions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePredictionsTable extends Migration
{
    public function up()
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_student_id')->constrained('student')->onDelete('cascade');
            $table->enum('predicted_status', ['lulus', 'lulus bersyarat', 'tidak lulus']);
            $table->integer('k_value');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('predictions');
    }
}
