<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create("micro_tasks", function (Blueprint $table) {
            $table->id();
            $table->foreignId("project_id")->constrained()->onDelete("cascade");
            $table->string("title");
            $table->json("assignees")->nullable();
            $table->date("due_date")->nullable();
            $table->enum("priority", ["low", "medium", "high", "urgent"])->default("low");
            $table->enum("status", ["pending", "in_progress", "completed"])->default("pending");
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists("micro_tasks");
    }
};
