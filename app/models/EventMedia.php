<?php

class EventMedia extends Eloquent {

	protected $table = "event_medias";
	protected $fillable = array("event_id", "media_id");

	public function event()
	{
		return $this->belongsTo("Event");
	}

	public function media()
	{
		return $this->belongsTo("Media");
	}
}
