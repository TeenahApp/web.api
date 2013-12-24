<?php

class MemberRelation extends Eloquent {

	protected $table = "member_relations";
	protected $fillable = array("member_a", "relationship", "member_b");

	public function firstMember()
	{
		return $this->belongsTo("Member", "member_a");
	}

	public function secondMember()
	{
		return $this->belongsTo("Member", "member_b");
	}
}
