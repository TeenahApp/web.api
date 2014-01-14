<?php

class Access extends Eloquent {

	protected $table = "accesses";
	protected $guarded = array();

	public function user()
	{
		return $this->belongTo("User");
	}
}
