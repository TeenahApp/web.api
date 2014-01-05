<?php

class Circle extends Eloquent {

	protected $table = "circles";
	protected $guarded = array();

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}

	public function members()
	{
		return $this->hasManyThrough("Member", "MemberCircle");
	}
}
