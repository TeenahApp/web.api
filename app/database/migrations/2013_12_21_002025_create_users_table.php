<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("users", function($table){
			$table->bigIncrements("id");
			$table->bigInteger("member_id");
			$table->string("username")->unique();
			$table->string("password");
			$table->string("email")->unique();
			$table->boolean("active")->default(1);
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
		//
		Schema::drop("users");
	}

}