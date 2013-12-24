<?php

class MemberPrivacy extends Eloquent {

	protected $table = "member_privacies";
	protected $fillable = array("member_id", "privacy_id");

	public function member()
	{
		return $this->belongsTo("Member");
	}

	public function privacy()
	{
		return $this->belongsTo("Privacy");
	}
}
