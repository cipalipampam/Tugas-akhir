<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_weight_ratios_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeightRatiosTable extends Migration
{
    public function up()
    {
        Schema::create('weight_ratios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_student_id')->constrained('student')->onDelete('cascade');
            $table->enum('class', ['lulus', 'lulus bersyarat', 'tidak lulus']);
            $table->decimal('weight_ratio', 10, 5);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('weight_ratios');
    }
}
