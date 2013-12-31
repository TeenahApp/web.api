<?php

class User extends Eloquent {

	protected $table = "users";
	protected $guarded = array();

	public function member()
	{
		return $this->belongsTo("Member");
	}

	public function accesses()
	{
		return $this->hasMany("Access");
	}

	// This method to get the current logged in user.
	public static function current()
	{
		$user_token = Request::header("X-User-Token");

		// Get the logged in user.
		$user = self::where("token", "=", $user_token)->first();

		return $user;
	}
}
