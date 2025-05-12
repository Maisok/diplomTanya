<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Уже было ограничение
            $table->text('description'); // Текст без ограничения длины
            $table->decimal('price', 8, 2);
            $table->string('image', 255)->nullable(); // Пути к файлам могут быть длинными
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
};
