<?php

class Message extends Eloquent {

	protected $table = "messages";
	protected $guarded = array();

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}
}
