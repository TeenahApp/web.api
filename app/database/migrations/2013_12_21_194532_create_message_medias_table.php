<?php

use Illuminate\Database\Migrations\Migration;

class CreateMessageMediasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("message_medias", function($table){
			$table->bigIncrements("id");
			$table->bigInteger("message_id");
			$table->bigInteger("media_id");
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
		Schema::dropIfExists("message_medias");
	}

}