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
        Schema::table('notifications', function (Blueprint $table) {
            // Adding user_id column and setting up foreign key constraint
            $table->unsignedBigInteger('user_id')->after('id'); // Adjust 'after' to place it correctly in your table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Adding description column
            $table->text('description')->nullable()->after('user_id'); // Adjust 'after' to place it correctly in your table
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Dropping the foreign key constraint and the column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            
            // Dropping the description column
            $table->dropColumn('description');
        });
    }
};
