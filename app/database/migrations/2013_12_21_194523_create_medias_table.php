<?php

use Illuminate\Database\Migrations\Migration;

class CreateMediasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("medias", function($table){
			$table->bigIncrements("id");
			$table->enum("category", array("image", "video", "sound"));
			$table->string("taste"); // A thumb for an image, or a snippet of video/sound.
			$table->string("url");
			$table->string("signature");
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
		Schema::drop("medias");
	}

}