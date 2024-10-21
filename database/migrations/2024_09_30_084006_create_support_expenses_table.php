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
        Schema::create('support_expenses', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('support_id');
            $table->decimal('advance_amount', 10, 2)->nullable();
            $table->decimal('expense_amount', 10, 2)->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('attachment')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->bigInteger('approved_by')->nullable()->comment('user_id');
            $table->dateTime('approved_date')->nullable();
            $table->boolean('is_complete')->default(false);
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
        Schema::dropIfExists('support_expenses');
    }
};
