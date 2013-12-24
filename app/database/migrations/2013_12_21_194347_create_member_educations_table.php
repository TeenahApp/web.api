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
			$table->enum("degree", array("none", "elementary", "intermediate", "secondary", "diploma", "licentiate", "bachelor", "master", "doctorate"))->default("none");
			$table->bigInteger("major_id");
			$table->integer("start_year")->nullable();
			$table->integer("finish_year")->nullable();
			$table->enum("status", array("ongoing", "finished", "pending", "dropped"))->default("ongoing");
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
		Schema::dropIfExists("member_educations");
	}

}