<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{ 
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('staff_id'); // Добавлено поле для хранения ID специалиста
            $table->dateTime('appointment_time');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
    
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade'); // Связь с таблицей staff
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};
