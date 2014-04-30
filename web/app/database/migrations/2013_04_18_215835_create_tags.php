<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTags extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tags', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('tag_title');
		});
		Schema::create('tags_users', function(Blueprint $table)
		{
			$table->integer('user_id')->unsigned();
			$table->integer('tag_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('tag_id')->references('id')->on('tags');
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
		Schema::drop('tags_users');
		Schema::drop('tags');
	}

}
