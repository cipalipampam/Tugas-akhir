<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_weight_calculations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeightCalculationsTable extends Migration
{
    public function up()
    {
        Schema::create('weight_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distance_calculation_id')->constrained('distance_calculations')->onDelete('cascade');
            $table->decimal('weight', 15, 6);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('weight_calculations');
    }
}
