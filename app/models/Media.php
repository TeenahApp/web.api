<?php

class Media extends Eloquent {

	protected $table = "medias";
	protected $fillable = array("category", "taste", "url", "signature", "created_by");
	protected $guarded = array("signature");

	public function creator()
	{
		return $this->hasOne("Member", "created_by", "id");
	}
}
