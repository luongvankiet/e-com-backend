<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(Product::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignIdFor(ProductVariant::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('product_name')->nullable();
            $table->unsignedBigInteger('quantity')->default(1);
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('discount')
                ->nullable()
                ->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
