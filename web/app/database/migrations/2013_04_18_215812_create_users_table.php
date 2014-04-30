<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('email')->unique();
			$table->string('email_token')->nullable();
			$table->string('password')->nullable();
			$table->string('first_name')->nullable();
			$table->string('last_name')->nullable();
			$table->string('professional_title')->nullable();
			$table->string('postal_code')->nullable();
			$table->string('phone_office')->nullable();
			$table->string('phone_cell')->nullable();
			$table->string('headshot')->nullable();
			$table->boolean('alerts_sms')->default(0);
			$table->boolean('alert_email')->default(1);
			$table->boolean('verified')->default(0);
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
		Schema::drop('users');
	}

}
