<?php

class Member extends Eloquent {

	protected $table = "members";
	protected $fillable = array("gender", "name", "dob", "is_alive");
	protected $hidden = array("mobile", "email", "home_phone", "work_phone", "marital_status", "blood_type");
}
