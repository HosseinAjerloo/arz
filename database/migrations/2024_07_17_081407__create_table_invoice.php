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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('bank_id')->nullable()->constrained('banks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('siteService_id')->nullable()->constrained('site_services')->cascadeOnUpdate()->cascadeOnDelete();

            $table->decimal('service_id_custom',10,1)->nullable()->comment('در صورتی پر میشود که کاربر بخواهد از سرویس سفارشی استفاده کند');
            $table->foreignId('disscount_code_id')->nullable()->constrained('disscount_codes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->bigInteger('final_amount')->nullable();
            $table->integer('time_price_of_dollars')->nullable();
            $table->enum('type', ['service', 'wallet','transmission'])->nullable();
            $table->enum('status',['requested','failed','finished'])->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
