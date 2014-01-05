<?php

class MemberCircle extends Eloquent {

	protected $table = "member_circles";
	protected $guarded = array();
	
	public function member()
	{
		return $this->belongsTo("Member");
	}

	public function circle()
	{
		return $this->belongsTo("Circle");
	}
}
