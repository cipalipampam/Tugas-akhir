<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_distance_calculations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistanceCalculationsTable extends Migration
{
    public function up()
    {
        Schema::create('distance_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_student_id')->constrained('student')->onDelete('cascade');
            $table->foreignId('training_data_id')->constrained('student')->onDelete('cascade');
            $table->decimal('distance', 10, 5);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('distance_calculations');
    }
}
