<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Добавляем колонку branch_id
            $table->unsignedBigInteger('branch_id')->after('staff_id');
            
            // Добавляем внешний ключ
            $table->foreign('branch_id')
                  ->references('id')
                  ->on('branches')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Удаляем внешний ключ
            $table->dropForeign(['branch_id']);
            
            // Удаляем колонку
            $table->dropColumn('branch_id');
        });
    }
};