<?php

class CircleMessageMemeber extends Eloquent {

	protected $table = "message_members";

	public function circle()
	{
		return $this->belongsTo("Circle");
	}

	public function member()
	{
		return $this->belongsTo("Member");
	}
}
