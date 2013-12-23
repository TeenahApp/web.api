<?php

class EventMedia extends Eloquent {

	protected $table = "event_medias";
	protected $fillable = array("event_id", "media_id");

	// TODO: Check if the relation is okay.
	public function event()
	{
		return $this->belongsTo("Event");
	}

	// TODO: Check this relation please.
	public function media()
	{
		return $this->hasOne("Media");
	}
}
