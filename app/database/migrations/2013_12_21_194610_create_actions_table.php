<?php

use Illuminate\Database\Migrations\Migration;

class CreateActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("actions", function($table){
			$table->bigIncrements("id");
			$table->enum("area", array("member", "event", "media", "member_comment", "event_comment", "media_comment"));
			$table->enum("action", array("view", "comment", "like", "flag"));
			$table->bigInteger("affected_member_id");
			$table->string("content")->nullable();
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
		Schema::dropIfExists("actions");
	}

}