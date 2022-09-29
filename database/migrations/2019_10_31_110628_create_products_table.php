<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('producer_id');
            $table->foreign('producer_id')->references('id')->on('producers');

            $table->string('name');
            $table->string('image');
            $table->string('sku_code');
            $table->string('monitor')->default('Đang cập nhật...');
            $table->string('front_camera')->default('Đang cập nhật...');
            $table->string('rear_camera')->default('Đang cập nhật...');
            $table->string('CPU')->default('Đang cập nhật...');
            $table->string('GPU')->default('Đang cập nhật...');
            $table->integer('RAM')->default(0);
            $table->integer('ROM')->default(0);
            $table->string('OS')->default('Đang cập nhật...');
            $table->string('pin')->default('Đang cập nhật...');
            $table->longText('information_details')->nullable();
            $table->longText('product_introduction')->nullable();
            $table->float('rate', 2, 1)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
