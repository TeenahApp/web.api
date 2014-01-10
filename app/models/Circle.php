<?php

class Circle extends Eloquent {

	protected $table = "circles";
	protected $guarded = array();

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}

	public function members()
	{
		// Set the circle right.
		$circle_id = $this->id;

		return DB::table("members")->select("members.*")->join("member_circles", function($join) use ($circle_id){
			$join->on("members.id", "=", "member_circles.member_id")
				->where("circle_id", "=", $circle_id);
		});
	}
}
