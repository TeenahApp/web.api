<?php

class Message extends Eloquent {

	protected $table = "messages";

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}
}
