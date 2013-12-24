<?php

class Media extends Eloquent {

	protected $table = "medias";
	protected $fillable = array("category", "taste", "url", "signature", "created_by");
	protected $hidden = array("signature");

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}
}
