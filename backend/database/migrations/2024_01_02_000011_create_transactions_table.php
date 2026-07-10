<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // mvola, orange_money, airtel_money
            $table->string('transaction_id')->unique(); // External transaction ID
            $table->string('server_correlation_id')->nullable(); // MVola server correlation ID
            $table->string('reference')->unique(); // Internal reference
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('MGA');
            $table->string('customer_phone');
            $table->string('merchant_number');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('ussd_code')->nullable();
            $table->text('qr_code_data')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['provider', 'status']);
            $table->index('transaction_id');
            $table->index('server_correlation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
