<?php

use Illuminate\Database\Migrations\Migration;

class CreateMemberJobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("member_jobs", function($table){
			$table->bigIncrements("id");
			$table->bigInteger("member_id");
			$table->string("title");
			$table->bigInteger("company_id");
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
		Schema::drop("member_jobs");
	}

}