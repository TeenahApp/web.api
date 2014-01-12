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
		return $this->hasMany("CircleEventMember", "event_id");
	}

	public function delete()
	{
		// Delete every invitation.
		CircleEventMember::where("event_id", "=", $this->id)->delete();

		// TODO: Delete every action.

		// Delete the event.
		return parent::delete();
	}
}
