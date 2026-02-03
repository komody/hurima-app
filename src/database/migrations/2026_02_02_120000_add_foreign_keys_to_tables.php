<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTables extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('table_products', function (Blueprint $table) {
      if (!Schema::hasColumn('table_products', 'seller_id')) {
        $table->unsignedBigInteger('seller_id')->after('id');
      }
      if (!Schema::hasColumn('table_products', 'buyer_id')) {
        $table->unsignedBigInteger('buyer_id')->nullable()->after('seller_id');
      }
      if (!Schema::hasColumn('table_products', 'condition_id')) {
        $table->unsignedBigInteger('condition_id')->after('buyer_id');
      }

      $table->foreign('seller_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table->foreign('buyer_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table->foreign('condition_id')
        ->references('id')
        ->on('table_conditions')
        ->onDelete('cascade');
    });

    Schema::table('table_likes', function (Blueprint $table) {
      if (!Schema::hasColumn('table_likes', 'user_id')) {
        $table->unsignedBigInteger('user_id')->after('id');
      }
      if (!Schema::hasColumn('table_likes', 'product_id')) {
        $table->unsignedBigInteger('product_id')->after('user_id');
      }

      $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table->foreign('product_id')
        ->references('id')
        ->on('table_products')
        ->onDelete('cascade');
    });

    Schema::table('table_comments', function (Blueprint $table) {
      if (!Schema::hasColumn('table_comments', 'user_id')) {
        $table->unsignedBigInteger('user_id')->after('id');
      }
      if (!Schema::hasColumn('table_comments', 'product_id')) {
        $table->unsignedBigInteger('product_id')->after('user_id');
      }

      $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table->foreign('product_id')
        ->references('id')
        ->on('table_products')
        ->onDelete('cascade');
    });

    Schema::table('table_product_categories', function (Blueprint $table) {
      if (!Schema::hasColumn('table_product_categories', 'product_id')) {
        $table->unsignedBigInteger('product_id')->after('id');
      }
      if (!Schema::hasColumn('table_product_categories', 'category_id')) {
        $table->unsignedBigInteger('category_id')->after('product_id');
      }

      $table->foreign('product_id')
        ->references('id')
        ->on('table_products')
        ->onDelete('cascade');

      $table->foreign('category_id')
        ->references('id')
          ->on('table_categories')
        ->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('table_product_categories', function (Blueprint $table) {
      $table->dropForeign(['product_id']);
      $table->dropForeign(['category_id']);
      $table->dropColumn(['product_id', 'category_id']);
    });

    Schema::table('table_comments', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
      $table->dropForeign(['product_id']);
      $table->dropColumn(['user_id', 'product_id']);
    });

    Schema::table('table_likes', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
      $table->dropForeign(['product_id']);
      $table->dropColumn(['user_id', 'product_id']);
    });

    Schema::table('table_products', function (Blueprint $table) {
      $table->dropForeign(['seller_id']);
      $table->dropForeign(['buyer_id']);
      $table->dropForeign(['condition_id']);
      $table->dropColumn(['seller_id', 'buyer_id', 'condition_id']);
    });
  }
}
