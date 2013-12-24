<?php

class Event extends Eloquent {

	protected $table = "events";
	protected $fillable = array("title", "start_datetime", "finish_datetime", "location", "created_by");
	protected $hidden = array("latitude", "longtitude");

	// TODO: To be validated.
	public function creator()
	{
		return $this->hasOne("Member", "created_by", "id");
	}
}
