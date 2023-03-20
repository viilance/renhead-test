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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('employee_type');
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->float('total_hours');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->foreign('employee_id')
                ->references('id')
                ->on('professors')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('employee_id')
                ->references('id')
                ->on('traders')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->after('employee_id')
                ->name('jobs_traders_employee_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
