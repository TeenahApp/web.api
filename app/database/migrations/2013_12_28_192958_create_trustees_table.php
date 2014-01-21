<?php

use Illuminate\Database\Migrations\Migration;

class CreateTrusteesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("trustees", function($table){
			$table->bigIncrements("id");
			$table->bigInteger("member_id");
			$table->bigInteger("trustee_id");
			$table->boolean("active")->default(1);
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
		Schema::dropIfExists("trustees");
	}

}