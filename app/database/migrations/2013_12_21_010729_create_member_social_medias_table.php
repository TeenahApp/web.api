<?php

use Illuminate\Database\Migrations\Migration;

class CreateMemberSocialMediasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("member_social_medias", function($table){
			$table->bigIncrements("id");
			$table->bigInteger("member_id");
			$table->bigInteger("social_media_id");
			$table->mediumText("account");
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
		Schema::dropIfExists("member_social_medias");
	}

}