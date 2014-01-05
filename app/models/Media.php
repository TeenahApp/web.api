<?php

class Media extends Eloquent {

	protected $table = "medias";
	protected $guarded = array();

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}
}
