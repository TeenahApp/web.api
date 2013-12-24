<?php

class MemberEducation extends Eloquent {

	protected $table = "member_educations";
	protected $fillable = array("member_id", "degree", "major_id");

	public function member()
	{
		return $this->belongsTo("Member");
	}

	public function major()
	{
		return $this->belongsTo("EducationMajor", "major_id");
	}
}
