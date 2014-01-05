<?php

class MemberPrivacy extends Eloquent {

	protected $table = "member_privacies";
	protected $guarded = array();

	public function member()
	{
		return $this->belongsTo("Member");
	}

	public function privacy()
	{
		return $this->belongsTo("Privacy");
	}
}
