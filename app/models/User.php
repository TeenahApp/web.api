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
}
