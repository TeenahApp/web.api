<?php

class User extends Eloquent {

	protected $table = "users";

	public function member()
	{
		return $this->belongsTo("Member");
	}

	public function accesses()
	{
		return $this->hasMany("Access");
	}
}
