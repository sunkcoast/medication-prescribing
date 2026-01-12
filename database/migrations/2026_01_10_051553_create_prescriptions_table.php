<?php

use App\Models\User;
use App\Models\Examination;
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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Examination::class)
                ->constrained()
                ->onDelete('cascade');
            $table->foreignIdFor(User::class, 'doctor_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignIdFor(User::class, 'pharmacist_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->dateTime('examined_at');
            $table->dateTime('locked_at')->nullable();
            $table->enum('status', ['pending', 'served', 'paid'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
