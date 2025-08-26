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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('uuid')->unique( );
            $table->string('phone');
            $table->string('email')->unique();
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('referred_chapter_name');
            $table->string('referred_by');
            $table->decimal('amount', 10, 2);
            $table->string('payment_status')->default('pending');
            $table->string('stripe_payment_id')->nullable();
            $table->string('membership_number')->unique();
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
}; 

// last date 2oct 2025, min 250 ticket max 2000 ticket / draw data 4 oct
// graphics /short video