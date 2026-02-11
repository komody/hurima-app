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
    Schema::table('products', function (Blueprint $table) {
      if (!Schema::hasColumn('products', 'seller_id')) {
        $table->unsignedBigInteger('seller_id')->after('id');
      }
      if (!Schema::hasColumn('products', 'buyer_id')) {
        $table->unsignedBigInteger('buyer_id')->nullable()->after('seller_id');
      }
      if (!Schema::hasColumn('products', 'condition_id')) {
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
        ->on('conditions')
        ->onDelete('cascade');
    });

    Schema::table('likes', function (Blueprint $table) {
      if (!Schema::hasColumn('likes', 'user_id')) {
        $table->unsignedBigInteger('user_id')->after('id');
      }
      if (!Schema::hasColumn('likes', 'product_id')) {
        $table->unsignedBigInteger('product_id')->after('user_id');
      }

      $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table->foreign('product_id')
        ->references('id')
        ->on('products')
        ->onDelete('cascade');
    });

    Schema::table('comments', function (Blueprint $table) {
      if (!Schema::hasColumn('comments', 'user_id')) {
        $table->unsignedBigInteger('user_id')->after('id');
      }
      if (!Schema::hasColumn('comments', 'product_id')) {
        $table->unsignedBigInteger('product_id')->after('user_id');
      }

      $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table->foreign('product_id')
        ->references('id')
        ->on('products')
        ->onDelete('cascade');
    });

    Schema::table('category_product', function (Blueprint $table) {
      if (!Schema::hasColumn('category_product', 'product_id')) {
        $table->unsignedBigInteger('product_id')->after('id');
      }
      if (!Schema::hasColumn('category_product', 'category_id')) {
        $table->unsignedBigInteger('category_id')->after('product_id');
      }

      $table->foreign('product_id')
        ->references('id')
        ->on('products')
        ->onDelete('cascade');

      $table->foreign('category_id')
        ->references('id')
        ->on('categories')
        ->onDelete('cascade');
    });

    Schema::table('accounts', function (Blueprint $table) {
      if (!Schema::hasColumn('accounts', 'user_id')) {
        $table->unsignedBigInteger('user_id')->after('id');
      }

      $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');
    });

    Schema::table('orders', function (Blueprint $table) {
      if (!Schema::hasColumn('orders', 'user_id')) {
        $table->unsignedBigInteger('user_id')->after('id');
      }
      if (!Schema::hasColumn('orders', 'product_id')) {
        $table->unsignedBigInteger('product_id')->after('user_id');
      }

      $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table->foreign('product_id')
        ->references('id')
        ->on('products')
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
    Schema::table('orders', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
      $table->dropForeign(['product_id']);
      $table->dropColumn(['user_id', 'product_id']);
    });

    Schema::table('accounts', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
      $table->dropColumn(['user_id']);
    });

    Schema::table('category_product', function (Blueprint $table) {
      $table->dropForeign(['product_id']);
      $table->dropForeign(['category_id']);
      $table->dropColumn(['product_id', 'category_id']);
    });

    Schema::table('comments', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
      $table->dropForeign(['product_id']);
      $table->dropColumn(['user_id', 'product_id']);
    });

    Schema::table('likes', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
      $table->dropForeign(['product_id']);
      $table->dropColumn(['user_id', 'product_id']);
    });

    Schema::table('products', function (Blueprint $table) {
      $table->dropForeign(['seller_id']);
      $table->dropForeign(['buyer_id']);
      $table->dropForeign(['condition_id']);
      $table->dropColumn(['seller_id', 'buyer_id', 'condition_id']);
    });
  }
}
