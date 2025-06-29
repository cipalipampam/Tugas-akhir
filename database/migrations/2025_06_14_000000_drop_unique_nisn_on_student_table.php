<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueNisnOnStudentTable extends Migration
{
    public function up()
    {
        Schema::table('student', function (Blueprint $table) {
            $table->dropUnique(['nisn']);
        });
    }

    public function down()
    {
        Schema::table('student', function (Blueprint $table) {
            $table->unique('nisn');
        });
    }
} 