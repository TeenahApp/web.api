<?php

class Action extends Eloquent {

	protected $table = "actions";
	protected $fillable = array("area", "action", "affected_member_id", "created_by");

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}

	public function affected()
	{
		return $this->belongsTo("Member", "affected_member_id");
	}
}
