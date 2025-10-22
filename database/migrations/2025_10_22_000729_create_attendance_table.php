<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->string('event', 20);
            $table->integer('event_id');
            $table->string('att', 1);
            $table->string('coop');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('firstname', 50);
            $table->string('lastname', 50);
            $table->string('title', 20);
            $table->tinyInteger('qtr');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
