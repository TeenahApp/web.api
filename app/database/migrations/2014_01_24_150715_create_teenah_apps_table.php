<?php

use Illuminate\Database\Migrations\Migration;

class CreateTeenahAppsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("teenah_apps", function($table){
			$table->bigIncrements("id");
			$table->string("email");
			$table->string("app_key");
			$table->string("app_secret");
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
		Schema::dropIfExists("teenah_apps");
	}

}