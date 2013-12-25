<?php

class Privacy extends Eloquent {

	protected $table = "privacies";

	public function members()
	{
		return $this->hasManyThrough("Member", "MemberPrivacy");
	}
}
