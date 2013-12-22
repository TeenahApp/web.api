<?php

use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("events", function($table){
			$table->bigIncrements("id");
			$table->string("title");
			$table->datetime("start_datetime");
			$table->datetime("finish_datetime");
			$table->string("location");
			$table->double("latitude");
			$table->double("longtitude");
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
		Schema::drop("events");
	}

}