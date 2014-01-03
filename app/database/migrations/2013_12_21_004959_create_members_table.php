<?php

use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("members", function($table){
			$table->bigIncrements("id");
			$table->enum("gender", array("male", "female"));
			$table->string("name");
			$table->string("fullname")->nullable()->default(null);
			$table->string("nickname")->nullable()->default(null);
			$table->date("dob")->nullable();
			$table->string("pob")->nullable();
			$table->date("dod")->nullable();
			$table->string("pod")->nullable();
			$table->integer("age")->default(0);
			$table->boolean("is_alive")->default(1);
			$table->string("photo")->nullable();
			$table->string("location")->nullable();
			$table->string("mobile")->nullable();
			$table->string("email")->nullable();
			$table->string("home_phone")->nullable();
			$table->string("work_phone")->nullable();
			$table->enum("marital_status", array("single", "married", "divorced", "widow"))->default("single");
			$table->string("blood_type")->nullable();
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
		Schema::dropIfExists("members");
	}

}