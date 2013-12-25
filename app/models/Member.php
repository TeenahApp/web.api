<?php

class Member extends Eloquent {

	protected $table = "members";
	protected $fillable = array("gender", "name", "dob", "is_alive");
	protected $hidden = array("mobile", "email", "home_phone", "work_phone", "marital_status", "blood_type");

	public function user()
	{
		return $this->hasOne("User", "mobile", "mobile");
	}

	public function circles()
	{
		return $this->hasManyThrough("Member", "MemberCircle");
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
		return $this->hasManyThrough("Member", "MemberRelation", "member_a");
	}

	// TODO: Find a comprehensive name.
	public function inRelations()
	{
		return $this->hasManyThrough("Member", "MemberRelation", "member_b");
	}

	public function socialMedias()
	{
		return $this->hasManyThrough("SocialMedia", "MemberSocialMedia");
	}
}
