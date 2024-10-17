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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type',['Bug','Feature','Improvement'])->default('Bug');
            $table->enum('status',['Open','InProgress','Completed','Blocked'])->default('Open');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium'); 
            $table->date('date_due');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
