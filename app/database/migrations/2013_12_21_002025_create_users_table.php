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
			$table->bigInteger("member_id")->default(0);
			$table->string("mobile")->unique();
			$table->string("token")->nullable();
			$table->string("email")->unique()->nullable();
			$table->boolean("active")->default(1);
			$table->datetime("last_time_tokenized")->default("0000-00-00");
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