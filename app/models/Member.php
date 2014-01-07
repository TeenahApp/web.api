<?php

class Member extends Eloquent {

	protected $table = "members";
	protected $guarded = array();

	public function user()
	{
		return $this->hasOne("User", "mobile", "mobile");
	}

	public function circles()
	{
		// Set the member right.
		$member_id = $this->id;

		return DB::table("circles")->select("circles.*")->join("member_circles", function($join) use ($member_id){
			$join->on("circles.id", "=", "member_circles.circle_id")
				->where("member_id", "=", $member_id);
		});
	}

	public function createdActions()
	{
		return $this->hasMany("Action", "created_by");
	}

	public function affectedActions()
	{
		return $this->hasMany("Action", "affected_member_id");
	}

	public function educations()
	{
		return $this->hasMany("MemberEducation");
	}

	// TODO: Check the foregin keys.
	// TODO: Check if this method is needed.
	public function educationMajors()
	{
		return $this->hasManyThrough("EducationMajor", "MemberEducation");
	}

	public function jobs()
	{
		return $this->hasMany("MemberJob");
	}

	// TODO: Check if this method is needed too.
	// OPINION: There is no need to complicate issues more.
	public function jobCompanies()
	{
		return $this->hasManyThrough("JobCompany", "MemberJob");
	}

	public function privacies()
	{
		return $this->hasManyThrough("Privacy", "MemberPrivacy");
	}

	// TODO: Find a fine name.
	public function outRelations()
	{
		return $this->hasMany( "MemberRelation", "member_a");
	}

	// TODO: Find a comprehensive name.
	public function inRelations()
	{
		return $this->hasMany("MemberRelation", "member_b");
	}

	public function socialMedias()
	{
		return $this->hasManyThrough("SocialMedia", "MemberSocialMedia");
	}

	// Normalize the name; by removing Harakat's, and Madd's.
	public static function normalize($name)
	{
		// Remove every non Arabic letters (chars).
		$name = preg_replace("/[^أاإآبتثجحخدذرزسشصضطظعغفقكلمنهوؤيئءىﻻﻵة ]/u", "", $name);

		// Normalize [Abd].
		$name = preg_replace("/عبد /", "عبد", $name);

		// Null the name if empty.
		$name = empty($name) ? null : $name;

		return $name;
	}

	// TODO:	There should be an override method for create().
	//			It fills the fullname of a member by moving up to fathers of.

	// One of the main methods for the system to decide if the logged in member can use a resource for another/same member.
	// TODO: Build this method to be real.
	public static function canUseResource($member_a, $member_b)
	{
		return true;
	}
}
