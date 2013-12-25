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
		DB::table("users")->delete();

		DB::table("users")->insert(
			array(
				"member_id" => 1,
				"username" => "966",
				"password" => Hash::make("123"),
				"created_at" => new DateTime()
			)
		);
	}
}

class MemberTableSeeder extends Seeder {

	public function run()
	{
		DB::table("members")->delete();

		DB::table("members")->insert(
			array(
				"id" => 1,
				"name" => "TeenahApp",
				"created_at" => new DateTime()
			)
		);
	}
}