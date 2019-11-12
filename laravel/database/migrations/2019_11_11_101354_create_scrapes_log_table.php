<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScrapesLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scrapes_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('source_id');
            $table->timestamp('started_at');
            $table->timestamp('finished_at');
            $table->integer('articles_analyzed');
            $table->integer('articles_imported');
            
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
        Schema::dropIfExists('scrapes_log');
    }
}
