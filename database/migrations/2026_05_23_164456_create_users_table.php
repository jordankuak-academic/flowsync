<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create("users", function (Blueprint $table) {
            $table->id();
            $table->foreignId("role_id")->constrained()->onDelete("cascade");
            $table->string("staff_id")->unique();
            $table->string("name");
            $table->string("username")->unique();
            $table->string("email")->unique();
            $table->timestamp("email_verified_at")->nullable();
            $table->string("password");
            $table->string("job_title");
            $table->string("ic_number")->unique();
            $table->string("contact")->unique();
            $table->boolean("is_active")->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists("users");
    }
};
