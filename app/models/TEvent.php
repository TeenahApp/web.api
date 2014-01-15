<?php

class TEvent extends Eloquent {

	protected $table = "events";
	protected $guarded = array();

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}

	public function members()
	{
		return $this->hasMany("CircleEventMember", "event_id")->with("member");
	}

	public function medias()
	{
		return $this->hasMany("EventMedia", "event_id")->with("media");
	}

	public function delete()
	{
		// Delete every invitation.
		CircleEventMember::where("event_id", "=", $this->id)->delete();

		// Delete every action.
		Action::where("area", "=", "event")->where("affected_id", "=", $this->id)->delete();

		// Delete the event.
		return parent::delete();
	}
}
