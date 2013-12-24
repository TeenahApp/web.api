<?php

class MemberJob extends Eloquent {

	protected $table = "member_jobs";
	protected $fillable = array("member_id", "title", "company_id");

	public function member()
	{
		return $this->belongsTo("Member");
	}

	public function company()
	{
		return $this->belongsTo("JobCompany", "company_id");
	}
}
