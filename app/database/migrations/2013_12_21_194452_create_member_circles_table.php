<?php

use Illuminate\Database\Migrations\Migration;

class CreateMemberCirclesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("member_circles", function($table){
			$table->bigIncrements("id");
			$table->bigInteger("member_id");
			$table->bigInteger("circle_id");
			$table->enum("status", array("active", "pending", "blocked"));
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
		Schema::drop("member_circles");
	}

}