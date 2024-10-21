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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->string('sku')->unique(); // Stock Keeping Unit (unique)
            $table->enum('unit',['kg','litre','piece','box','pack','bottle','can','dozen','gram','milligram','milliliter','ounce','pint','pound','ton','yard',
            ])->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->bigInteger('brand_id')->nullable();
            $table->string('barcode')->nullable();
            $table->json('tags')->nullable();
            $table->decimal('regular_price', 10, 2)->default(0.00);
            $table->decimal('sale_price', 10, 2)->default(0.00);
            $table->boolean('is_description_shown_in_invoices')->default(false);
            $table->boolean('has_related_products')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('has_serials')->default(false);
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
        Schema::dropIfExists('products');
    }
};
