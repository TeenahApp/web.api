<?php

class EventMedia extends Eloquent {

	protected $table = "event_medias";
	protected $guarded = array();

	public function event()
	{
		return $this->belongsTo("Event");
	}

	public function media()
	{
		return $this->belongsTo("Media");
	}
}
