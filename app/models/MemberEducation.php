<?php

class MemberEducation extends Eloquent {

	protected $table = "member_educations";
	protected $guarded = array();

	public function member()
	{
		return $this->belongsTo("Member");
	}

	public function major()
	{
		return $this->belongsTo("EducationMajor", "major_id");
	}
}
