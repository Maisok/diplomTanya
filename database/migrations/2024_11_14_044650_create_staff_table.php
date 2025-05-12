<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50); // Уже было ограничение
            $table->string('last_name', 50); // Уже было ограничение
            $table->string('middle_name', 50)->nullable(); // Уже было ограничение
            $table->string('image', 255)->nullable(); // Путь к файлу
            $table->string('phone', 15)->unique(); // Добавил ограничение как в users
            $table->string('password'); // Без ограничения для хэша
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff');
    }
};
