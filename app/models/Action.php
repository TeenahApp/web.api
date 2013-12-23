<?php

class Action extends Eloquent {

	protected $table = "actions";
	protected $fillable = array("area", "action", "affected_member_id", "created_by");

	// TODO: Check if the relation is in a correct shape.
	public function creator()
	{
		return $this->belongsTo("Member", "created_by", "id");
	}

	// TODO: Check if the relation is in a correct shape.
	public function affected()
	{
		return $this->hasOne("Member", "affected_member_id", "id");
	}
}
