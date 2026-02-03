<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('image_url', 255);
            $table->string('brand_name', 255)->nullable();
            $table->integer('price');
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->text('description');
            $table->string('delivery_address', 255);
            $table->boolean('sold_out')->default(false);
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
        Schema::dropIfExists('table_products');
    }
}
