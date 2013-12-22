<?php

use Illuminate\Database\Migrations\Migration;

class CreateMemberEducationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("member_educations", function($table){
			$table->bigIncrements("id");
			$table->bigInteger("member_id");
			$table->enum("degree", array("none", "elementary", "intermediate", "secondary", "diploma", "licentiate", "bachelor", "master", "doctorate"));
			$table->bigInteger("major_id");
			$table->integer("start_year");
			$table->integer("finish_year");
			$table->enum("status", array("ongoing", "finished", "pending", "dropped"));
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
		Schema::drop("member_educations");
	}

}