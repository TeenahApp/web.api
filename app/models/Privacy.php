<?php

class Privacy extends Eloquent {

	protected $table = "privacies";
	protected $guarded = array();

	public function members()
	{
		return $this->hasManyThrough("Member", "MemberPrivacy");
	}
}
