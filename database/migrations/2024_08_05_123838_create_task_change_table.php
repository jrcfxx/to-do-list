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
        Schema::create('task_change', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->longText('changed_field');
            $table->longText('old_value');
            $table->longText('new_value');
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('task')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_change', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
        });
        Schema::dropIfExists('task_change');
    }
};
