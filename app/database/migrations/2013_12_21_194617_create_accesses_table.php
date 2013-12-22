<?php

use Illuminate\Database\Migrations\Migration;

class CreateAccessesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("accesses", function($table){
			$table->bigIncrements("id");
			$table->bigInteger("user_id");
			$table->enum("category", array("login", "logout"));
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
		Schema::drop("accesses");
	}

}