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
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('phase', ['nomination', 'voting', 'closed'])->default('nomination');
            $table->integer('max_nominations');
            $table->integer('max_winners');
            $table->json('winner_labels')->nullable();
            $table->timestamp('nomination_ends_at')->nullable();
            $table->timestamp('voting_ends_at')->nullable();
            $table->timestamp('votes_reset_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elections');
    }
};
