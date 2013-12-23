<?php

class CircleEventMemeber extends Eloquent {

	protected $table = "circle_event_members";
	protected $fillable = array("circle_id", "event_id", "member_id");

	// TODO: This is a controversial class and it needs to be validated.
	// TODO: Likely, has many through will be used.
}
