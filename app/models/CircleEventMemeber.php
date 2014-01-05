<?php

class CircleEventMemeber extends Eloquent {

	protected $table = "circle_event_members";
	protected $guarded = array();

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
