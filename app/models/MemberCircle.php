<?php

class MemberCircle extends Eloquent {

	protected $table = "member_circles";
	protected $fillable = array("member_id", "circle_id");
	
	// TODO: Check if these relations are well.
	public function member()
	{
		return $this->belongsTo("Member");
	}

	// TODO: Check this too.
	public function circle()
	{
		return $this->belongsTo("Circle");
	}
}
