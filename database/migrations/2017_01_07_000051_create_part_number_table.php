<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartNumberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_numbers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('item_code');
            $table->string('inc', 5)->default('');
            $table->string('item_name');
            $table->string('short_desc', 50);
            $table->string('man_code', 10);
            $table->string('man_name')->default('');
            $table->string('part_number');
            $table->text('po_text')->default('');
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
        Schema::dropIfExists('part_numbers');
    }
}
