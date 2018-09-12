<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    if(!Schema::connection('dbp_users')->hasTable('messages')) {
		    Schema::connection('dbp_users')->create('messages', function (Blueprint $table) {
			    $table->increments('id');
			    $table->integer('user_id')->unsigned()->nullable();
			    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
			    $table->boolean('resolved')->default(0);
			    $table->string('email', 256);
			    $table->string('subject');
			    $table->string('purpose');
			    $table->text('message');
			    $table->timestamp('created_at')->useCurrent();
			    $table->timestamp('updated_at')->useCurrent();
		    });
	    }

	    if(!Schema::connection('dbp_users')->hasTable('message_responses')) {
		    Schema::connection('dbp_users')->create('message_responses', function (Blueprint $table) {
			    $table->increments('id');
			    $table->integer('message_id')->unsigned()->nullable();
			    $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade')->onUpdate('cascade');
			    $table->integer('user_id')->unsigned()->nullable();
			    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
			    $table->string('subject');
			    $table->text('message');
			    $table->timestamp('created_at')->useCurrent();
			    $table->timestamp('updated_at')->useCurrent();
		    });
	    }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbp_users')->dropIfExists('messages');
	    Schema::connection('dbp_users')->dropIfExists('message_responses');
    }
}