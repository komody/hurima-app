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
    Schema::table('items', function (Blueprint $table) {
      if (!Schema::hasColumn('items', 'seller_id')) {
        $table->unsignedBigInteger('seller_id')->after('id');
      }
      if (!Schema::hasColumn('items', 'buyer_id')) {
        $table->unsignedBigInteger('buyer_id')->nullable()->after('seller_id');
      }
      if (!Schema::hasColumn('items', 'condition_id')) {
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
      if (!Schema::hasColumn('likes', 'item_id')) {
        $table->unsignedBigInteger('item_id')->after('user_id');
      }

      $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table->foreign('item_id')
        ->references('id')
        ->on('items')
        ->onDelete('cascade');
    });

    Schema::table('comments', function (Blueprint $table) {
      if (!Schema::hasColumn('comments', 'user_id')) {
        $table->unsignedBigInteger('user_id')->after('id');
      }
      if (!Schema::hasColumn('comments', 'item_id')) {
        $table->unsignedBigInteger('item_id')->after('user_id');
      }

      $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table->foreign('item_id')
        ->references('id')
        ->on('items')
        ->onDelete('cascade');
    });

    Schema::table('category_item', function (Blueprint $table) {
      if (!Schema::hasColumn('category_item', 'item_id')) {
        $table->unsignedBigInteger('item_id')->after('id');
      }
      if (!Schema::hasColumn('category_item', 'category_id')) {
        $table->unsignedBigInteger('category_id')->after('item_id');
      }

      $table->foreign('item_id')
        ->references('id')
        ->on('items')
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
      if (!Schema::hasColumn('orders', 'item_id')) {
        $table->unsignedBigInteger('item_id')->after('user_id');
      }

      $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');

      $table->foreign('item_id')
        ->references('id')
        ->on('items')
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
      $table->dropForeign(['item_id']);
      $table->dropColumn(['user_id', 'item_id']);
    });

    Schema::table('accounts', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
      $table->dropColumn(['user_id']);
    });

    Schema::table('category_item', function (Blueprint $table) {
      $table->dropForeign(['item_id']);
      $table->dropForeign(['category_id']);
      $table->dropColumn(['item_id', 'category_id']);
    });

    Schema::table('comments', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
      $table->dropForeign(['item_id']);
      $table->dropColumn(['user_id', 'item_id']);
    });

    Schema::table('likes', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
      $table->dropForeign(['item_id']);
      $table->dropColumn(['user_id', 'item_id']);
    });

    Schema::table('items', function (Blueprint $table) {
      $table->dropForeign(['seller_id']);
      $table->dropForeign(['buyer_id']);
      $table->dropForeign(['condition_id']);
      $table->dropColumn(['seller_id', 'buyer_id', 'condition_id']);
    });
  }
}
