<?php

class MemberCircle extends Eloquent {

	protected $table = "member_circles";
	protected $fillable = array("member_id", "circle_id");
	
	public function member()
	{
		return $this->belongsTo("Member");
	}

	public function circle()
	{
		return $this->belongsTo("Circle");
	}
}
