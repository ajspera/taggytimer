<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('timers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('start')->unsigned();
			$table->integer('end')->unsigned();
			$table->integer('duration')->unsigned()->nullable();
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->timestamps();
		});
		Schema::create('tags_timers', function(Blueprint $table)
		{
			$table->integer('tag_id')->unsigned();
			$table->integer('first_timer_id')->unsigned();
			$table->integer('last_timer_id')->unsigned()->nullable();
			$table->integer('timer_id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('timer_id')->references('id')->on('timers');
			$table->foreign('first_timer_id')->references('id')->on('timers');
			$table->foreign('last_timer_id')->references('id')->on('timers');
			$table->foreign('tag_id')->references('id')->on('tags');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tags_timers');
		Schema::drop('timers');
	}

}
