<?php

use Illuminate\Database\Migrations\Migration;

class CreateCirclesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("circles", function($table){
			$table->bigIncrements("id");
			$table->string("name");
			$table->integer("members_count");
			$table->boolean("active")->default(1);
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
		Schema::drop("circles");
	}

}