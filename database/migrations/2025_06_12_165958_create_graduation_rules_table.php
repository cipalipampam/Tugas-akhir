<?php

// database/migrations/xxxx_xx_xx_create_graduation_rules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGraduationRulesTable extends Migration
{
    public function up()
    {
        Schema::create('graduation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('attribute');         // Misal: 'rata_rata_semester', 'usp', 'sikap', dst.
            $table->string('operator');          // Misal: '>=', '<', '=', dst.
            $table->float('value')->nullable();  // Nilai pembanding (untuk numerik)
            $table->string('value_text')->nullable(); // Untuk nilai non-numerik (misal: 'baik')
            $table->string('category');          // Hasil kategori: 'LULUS', 'LULUS_BERSYARAT', 'TIDAK_LULUS'
            $table->integer('priority')->default(1); // Untuk urutan evaluasi aturan
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('graduation_rules');
    }
}
