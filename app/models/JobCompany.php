<?php

class JobCompany extends Eloquent {

	protected $table = "job_companies";
	protected $fillable = array("name", "category");
	protected $guarded = array("category");
}
