<?php

use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("messages", function($table){
			$table->bigIncrements("id");
			$table->enum("category", array("text", "update")); // Update will accept HTML.
			$table->bigInteger("created_by");
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
		Schema::drop("messages");
	}

}