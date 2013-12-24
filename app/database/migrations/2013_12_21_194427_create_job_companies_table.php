<?php

use Illuminate\Database\Migrations\Migration;

class CreateJobCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("job_companies", function($table){
			$table->bigIncrements("id");
			$table->string("name");
			$table->enum("category", array("private_limited", "joint_socket", "general_partnership", "limited_partnership", "foregin", "indivisual_establishment"))->nullable();
			$table->string("link")->nullable();
			$table->string("logo")->nullable();
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
		Schema::dropIfExists("job_companies");
	}

}