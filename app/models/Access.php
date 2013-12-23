<?php

class Access extends Eloquent {

	protected $table = "accesses";
	protected $fillable = array("user_id", "category");
}
