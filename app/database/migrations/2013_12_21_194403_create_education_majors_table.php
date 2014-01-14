<?php

use Illuminate\Database\Migrations\Migration;

class CreateEducationMajorsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("education_majors", function($table){
			$table->bigIncrements("id");
			$table->string("name");
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
		Schema::dropIfExists("education_majors");
	}

}