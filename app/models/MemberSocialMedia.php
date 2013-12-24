<?php

class MemberSocialMedia extends Eloquent {

	protected $table = "member_social_medias";

	public function member()
	{
		return $this->belongsTo("Member");
	}

	public function socialMedia()
	{
		return $this->belongsTo("SocialMedia");
	}
}
