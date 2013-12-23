<?php

use Illuminate\Database\Migrations\Migration;

class CreateMemberPrivaciesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("member_privacies", function($table){
			$table->bigIncrements("id");
			$table->bigInteger("member_id");
			$table->bigInteger("privacy_id");
			$table->enum("category", array("all", "home", "family"));
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
		Schema::drop("member_privacies");
	}

}