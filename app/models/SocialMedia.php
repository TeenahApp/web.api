<?php

class SocialMedia extends Eloquent {

	protected $table = "social_medias";
	protected $guarded = array();

	public function members()
	{
		return $this->hasManyThrough("Member", "MemberSocialMedia");
	}
}
