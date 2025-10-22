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
        Schema::create('journal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->string('flags')->nullable();
            $table->decimal('hours', 8, 2);
            $table->boolean('billable')->default(true);
            $table->text('team_note')->nullable();
            $table->text('client_note')->nullable();
            $table->text('retreat_note')->nullable();
            $table->date('retreat_date1')->nullable();
            $table->date('retreat_date2')->nullable();
            $table->boolean('qtr_inc')->default(false);
            $table->text('quarterly')->nullable();
            $table->longText('intro')->nullable();
            $table->boolean('retain')->default(false);
            $table->date('date');
            $table->string('category')->comment('call,research,quarterly');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal');
    }
};
