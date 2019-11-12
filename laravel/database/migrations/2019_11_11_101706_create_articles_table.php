<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('source_id');
            $table->string('original_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('link');
            $table->timestamp('date_pub');
            $table->timestamp('date_mod');
            $table->longText('content');
            $table->longText('excerpt')->nullable();
            $table->string('image')->nullable();
            $table->integer('featured');
            $table->timestamps();

            $table->foreign('source_id')->references('id')->on('sources')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
