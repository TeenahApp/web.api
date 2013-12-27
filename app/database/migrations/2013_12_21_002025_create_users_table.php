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
			$table->string("mobile")->unique();
			$table->string("token");
			$table->string("email")->unique()->nullable();
			$table->boolean("active")->default(1);
			$table->datetime("last_time_tokenized");
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
		Schema::dropIfExists("users");
	}

}