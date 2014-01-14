<?php

use Illuminate\Database\Migrations\Migration;

class CreateSocialMediasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("social_medias", function($table){
			$table->bigIncrements("id");
			$table->string("name");
			$table->string("pattern");
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
		Schema::dropIfExists("social_medias");
	}

}