<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call("UserTableSeeder");
		$this->call("MemberTableSeeder");
	}

}

class UserTableSeeder extends Seeder {

	public function run()
	{
		User::create(
			array(
				"member_id" => 1,
				"mobile" => "966"
			)
		);
	}
}

class MemberTableSeeder extends Seeder {

	public function run()
	{
		Member::create(
			array(
				"id" => 1,
				"name" => "TeenahApp",
				"mobile" => "966"
			)
		);
	}
}

// TODO: Insert all majors we know.
// TODO: Insert companies we know.