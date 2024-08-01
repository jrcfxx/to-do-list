<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('task', function (Blueprint $table) {
            if (!Schema::hasColumn('task', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
            }
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('task_change', function (Blueprint $table) {
            if (!Schema::hasColumn('task_change', 'task_id')) {
                $table->unsignedBigInteger('task_id')->after('id');
            }
            $table->foreign('task_id')->references('id')->on('task');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('task_change', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->dropColumn('task_id');
        });
    }
};
