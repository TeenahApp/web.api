<?php

class CircleEventMemeber extends Eloquent {

	protected $table = "circle_event_members";
	protected $fillable = array("circle_id", "event_id", "member_id");

	public function circle()
	{
		return $this->belongsTo("Circle");
	}

	public function event()
	{
		return $this->belongsTo("Event");
	}

	public function member()
	{
		return $this->belongsTo("Member");
	}
}
