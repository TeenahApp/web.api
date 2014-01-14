<?php

class MessageMedia extends Eloquent {

	protected $table = "message_medias";
	protected $guarded = array();

	public function message()
	{
		return $this->belongsTo("Message");
	}

	public function media()
	{
		return $this->belongsTo("Media");
	}
}
