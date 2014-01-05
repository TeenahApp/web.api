<?php

class Event extends Eloquent {

	protected $table = "events";
	protected $guarded = array();

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}
}
