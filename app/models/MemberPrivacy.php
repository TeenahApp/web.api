<?php

class MemberPrivacy extends Eloquent {

	protected $table = "member_privacies";
	protected $fillable = array("member_id", "privacy_id");

	// TODO: Check these.
	public function member()
	{
		return $this->belongsTo("Member");
	}

	// TODO: Check this too.
	public function privacy()
	{
		return $this->hasOne("Privacy");
	}
}
