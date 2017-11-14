<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReplyWord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reply_word', function (Blueprint $table) {
            $table->unsignedInteger('word_id');
            $table->unsignedInteger('reply_id');
            $table->unsignedInteger('repeat');
            $table->timestamps();

            $table->foreign('word_id')->references('id')->on('words')->onDelete('cascade');
            $table->foreign('reply_id')->references('id')->on('replies')->onDelete('cascade');
            $table->primary(['word_id', 'reply_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reply_word');
    }
}
