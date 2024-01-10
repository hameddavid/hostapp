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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->double('amount', 8, 2);
            $table->double('part_pay', 8, 2)->default(0.00);
            $table->enum('payment_status', ['PENDING','SUCCESS'])->default('PENDING');
            $table->string('invoiceReference');
            $table->string('transactionReference');
            $table->string('url');
            $table->string('account_number');
            $table->date('payment_date_time');
            $table->char('deleted', 1)->default('N');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('payments');
    }
};
