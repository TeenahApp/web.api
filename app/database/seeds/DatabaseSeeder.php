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

		$this->call("UsersTableSeeder");
		$this->call("MembersTableSeeder");
		$this->call("SocialMediasSeeder");
		$this->call("TeenahAppsTableSeeder");
	}

}

class UsersTableSeeder extends Seeder {

	public function run()
	{
		User::create(
			array(
				"member_id" => 1,
				"mobile" => "966",
				"token" => Hash::make("123") // TODO: This is for development/testing only.
			)
		);
	}
}

class MembersTableSeeder extends Seeder {

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
// TODO: Insert all social medias we know.
class SocialMediasSeeder extends Seeder {

	public function run()
	{
		// Facebook
		SocialMedia::create(array(
			"name" => "Facebook",
			"pattern" => "https://www.facebook.com/{account}"
		));

		// Twitter
		SocialMedia::create(array(
			"name" => "Twitter",
			"pattern" => "https://twitter.com/{account}"
		));

		// Photos sharing.
		SocialMedia::create(array(
			"name" => "Flickr", // Or/And Instagram.
			"pattern" => "https://www.flickr.com/photos/{account}"
		));

		// Video sharing/broadcasting.
		SocialMedia::create(array(
			"name" => "Youtube",
			"pattern" => "https://www.youtube.com/user/{account}"
		));
	}
}

// class TeenahAppsTableSeeder extends Seeder {

// 	public function run()
// 	{
// 		TeenahApp::create(
// 			array(
// 				"id" => 1,
// 				"email" => "teenah.app@gmail.com",
// 			)
// 		);
// 	}
// }