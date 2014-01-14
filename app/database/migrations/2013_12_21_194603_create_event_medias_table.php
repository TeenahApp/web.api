<?php

use Illuminate\Database\Migrations\Migration;

class CreateEventMediasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("event_medias", function($table){
			$table->bigIncrements("id");
			$table->bigInteger("event_id");
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
		Schema::dropIfExists("event_medias");
	}

}