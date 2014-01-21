<?php

class MemberSocialMedia extends Eloquent {

	protected $table = "member_social_medias";
	protected $guarded = array();

	public function member()
	{
		return $this->belongsTo("Member");
	}

	public function socialMedia()
	{
		return $this->belongsTo("SocialMedia");
	}

	public static function accounts($member_id)
	{
		return DB::table("member_social_medias")
				->select(
						"member_social_medias.id AS id",
						"social_medias.name AS social_media",
						"member_social_medias.account AS account",
						DB::raw("REPLACE(social_medias.pattern, '{account}', member_social_medias.account) AS url"),
						"member_social_medias.created_at",
						"member_social_medias.updated_at"
				)
				->join("social_medias", function($join){
					$join->on("member_social_medias.social_media_id", "=", "social_medias.id");
				})->where("member_id", "=", $member_id);
	}
}
