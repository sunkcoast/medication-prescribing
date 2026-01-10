<?php

use App\Models\User;
use App\Models\Patient;
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
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'doctor_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignIdFor(Patient::class)
                ->constrained()
                ->onDelete('cascade');
            $table->dateTime('exam_time');
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->integer('systole')->nullable();
            $table->integer('diastole')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->integer('respiration_rate')->nullable();
            $table->float('temperature')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
