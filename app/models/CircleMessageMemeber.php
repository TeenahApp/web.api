<?php

class CircleMessageMemeber extends Eloquent {

	protected $table = "circle_message_members";
	protected $guarded = array();

	public function circle()
	{
		return $this->belongsTo("Circle");
	}

	public function message()
	{
		return $this->belongsTo("Message");
	}

	public function member()
	{
		return $this->belongsTo("Member");
	}
}
