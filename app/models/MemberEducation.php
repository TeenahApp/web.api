<?php

class MemberEducation extends Eloquent {

	protected $table = "member_educations";
	protected $fillable = array("member_id", "degree", "major_id");

	public function major()
	{
		return $this->hasOne("EducationMajor", "major_id", "id");
	}
}
