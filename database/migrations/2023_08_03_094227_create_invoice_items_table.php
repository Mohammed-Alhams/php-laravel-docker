<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained("invoices")->cascadeOnDelete();
            $table->bigInteger('stock_id')->constrained("stocks")->cascadeOnDelete();
            $table->unsignedSmallInteger('quantity')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
};
