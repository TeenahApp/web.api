<?php

class Event extends Eloquent {

	protected $table = "events";
	protected $fillable = array("title", "start_datetime", "finish_datetime", "location", "created_by");
	protected $hidden = array("latitude", "longtitude");

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}
}
