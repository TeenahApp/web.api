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
			$table->string("fullname");
			$table->string("nickname");
			$table->date("dob");
			$table->string("pob");
			$table->date("dod");;
			$table->string("pod");
			$table->integer("age");
			$table->boolean("is_alive")->default(1);
			$table->string("photo");
			$table->string("location");
			$table->string("mobile");
			$table->string("email");
			$table->string("home_phone");
			$table->string("work_phone");
			$table->enum("marital_status", array("single", "married", "divorced", "widow"));
			$table->string("blood_type");
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
		Schema::drop("members");
	}

}