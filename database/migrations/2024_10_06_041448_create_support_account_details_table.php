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
        Schema::create('support_account_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('support_id');
            $table->bigInteger('support_expense_id')->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->string('payment_method')->nullable();
            $table->longText('description')->nullable()->comment('remarks');
            $table->string('attachment')->nullable();
            $table->string('transition_id')->nullable();
            $table->boolean('is_advance_paid')->default(false);
            $table->boolean('is_refund_paid')->default(false);
            $table->boolean('is_active')->default(true);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('organization_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_account_details');
    }
};
