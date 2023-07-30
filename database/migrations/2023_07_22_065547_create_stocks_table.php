<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained('stores')->cascadeOnDelete();
            $table->string('name');
            $table->bigInteger('barcode')->unique();
            $table->integer("quantity_by_units");
            $table->double('unit_price');
            $table->double('box_price');
            $table->double('box_wholesale_price');
            $table->double('unit_wholesale_price');
            $table->integer("quantity_by_boxes");
            $table->bigInteger("expiration_date")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}
