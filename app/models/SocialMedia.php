<?php

class SocialMedia extends Eloquent {

	protected $table = "social_medias";

	public function members()
	{
		return $this->hasManyThrough("Member", "MemberSocialMedia");
	}
}
