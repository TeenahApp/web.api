<?php

use Illuminate\Database\Migrations\Migration;

class CreateCircleMessageMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("circle_message_members", function($table){
			$table->bigIncrements("id");
			$table->bigInteger("circle_id");
			$table->bigInteger("member_id");
			$table->enum("status", array("pending", "sent", "read"));
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
		Schema::drop("circle_message_members");
	}

}