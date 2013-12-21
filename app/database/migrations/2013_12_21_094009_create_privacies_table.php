<?php

use Illuminate\Database\Migrations\Migration;

class CreatePrivaciesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("privacies", function($table){
			$table->increments("id");
			$table->string("name");
			$table->string("label");
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
		Schema::drop("privacies");
	}

}