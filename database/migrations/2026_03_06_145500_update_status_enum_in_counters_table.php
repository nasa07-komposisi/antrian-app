<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('counters', function (Blueprint $table) {
            // Change status enum to include 'offline'
            $table->enum('status', ['active', 'inactive', 'busy', 'offline'])->default('offline')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('counters', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'busy'])->default('active')->change();
        });
    }
};
