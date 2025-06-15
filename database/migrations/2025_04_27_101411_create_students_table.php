<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_students_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('student', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 20)->unique();
            $table->string('name');
            $table->string('tahun_angkatan', 4)->nullable();
            $table->enum('jenis_data', ['training', 'testing'])->default('testing');
            $table->string('true_status')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}