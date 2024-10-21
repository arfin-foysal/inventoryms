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
        Schema::create('supports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('support_type_id');
            $table->dateTime('assign_date');
            $table->dateTime('deadline')->nullable();
            $table->bigInteger('sale_id');
            $table->bigInteger('product_id')->nullable();
            $table->string('employee_ids')->comment('user_id of employee assigned to support add');
            $table->enum('status', ['pending', 'on_going', 'completed', 'rejected'])->default('pending');
            $table->string('name')->nullable();
            $table->longText('task')->nullable();
            $table->string('attachment')->nullable();
            $table->decimal('total_payment', 10, 2)->default(0.00);
            $table->decimal('advance_payment', 10, 2)->default(0.00);
            $table->decimal('refund_payment', 10, 2)->default(0.00);
            $table->boolean('accepted')->default(false);
            $table->boolean('is_active')->default(true);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('accepted_by')->nullable();
            $table->dateTime('accepted_date')->nullable();
            $table->bigInteger('organization_id')->nullable();
            $table->text('contact_info')->nullable();
            $table->text('address')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_assigns');
    }
};
