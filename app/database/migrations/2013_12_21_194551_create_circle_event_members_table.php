<?php

use Illuminate\Database\Migrations\Migration;

class CreateCircleEventMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("circle_event_members", function($table){
			$table->bigIncrements("id");
			$table->bigInteger("circle_id");
			$table->bigInteger("event_id");
			$table->bigInteger("member_id");
			$table->enum("decision", array("notyet", "willcome", "apologize"));
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
		Schema::drop("circle_event_members");
	}

}