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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3);
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->string('program', 20);
            $table->decimal('total_hours', 11, 2)->comment('current total');
            $table->decimal('q_hours', 11, 1)->comment('Hours alloted per Q');
            $table->text('address');
            $table->string('city');
            $table->string('state', 2);
            $table->string('zip', 10);
            $table->string('url');
            $table->string('contact_details');
            $table->tinyInteger('BalancedHrsUse');
            $table->tinyInteger('UsingPG');
            $table->date('CBLDSince');
            $table->date('ExpireDate');
            $table->tinyInteger('Expansion');
            $table->tinyInteger('NewGM');
            $table->integer('Retain');
            $table->date('RetreatDate');
            $table->longText('RetreatDesc');
            $table->string('gm_name');
            $table->string('gm_contact');
            $table->string('gm_email');
            $table->string('chair_name');
            $table->string('chair_contact');
            $table->string('chair_email');
            $table->string('board_name');
            $table->string('board_contact');
            $table->string('board_email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
