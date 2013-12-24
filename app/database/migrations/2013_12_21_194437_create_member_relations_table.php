<?php

use Illuminate\Database\Migrations\Migration;

class CreateMemberRelationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("member_relations", function($table){
			$table->bigIncrements("id");
			$table->bigInteger("member_a");
			$table->enum("relationship", array("father", "mother", "brother", "sister", "child", "wife", "husband", "stepfather", "stepmother", "stepchild", "breastfeeding_mother", "breastfeeding_child"))->nullable();
			$table->bigInteger("member_b");
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
		Schema::dropIfExists("member_relations");
	}

}