<?php

class Action extends Eloquent {

	protected $table = "actions";
	protected $guarded = array();

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}

	public function affected()
	{
		return $this->belongsTo("Member", "affected_member_id");
	}
}
