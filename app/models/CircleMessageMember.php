<?php

class CircleMessageMember extends Eloquent {

	protected $table = "circle_message_members";
	protected $guarded = array();

	public function circle()
	{
		return $this->belongsTo("Circle");
	}

	public function message()
	{
		return $this->belongsTo("Message")->with("medias");
	}

	public function member()
	{
		return $this->belongsTo("Member");
	}

	public static function broadcast($circle_id, $message_id, $member_id)
	{
		$deliveredMessage = self::create(array(
			"circle_id" => $circle_id,
			"message_id" => $message_id,
			"member_id" => $member_id,
			"status" => "sent"
		));

		// TODO: There has to be some kind of [push] notification.
		return $deliveredMessage;
	}
}
