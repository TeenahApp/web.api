<?php

class MemberRelation extends Eloquent {

	protected $table = "member_relations";
	protected $fillable = array("member_a", "relationship", "member_b");

	// TODO: Check if these are in the correct shape.
	public function firstMember()
	{
		return $this->belongsTo("Member", "member_a", "id");
	}

	// TODO: I am not really sure about this; but I will fix it.
	public function secondMember()
	{
		return $this->belongsTo("Member", "member_b", "id");
	}
}
