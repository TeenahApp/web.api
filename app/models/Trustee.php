<?php

class Trustee extends Eloquent {

	protected $table = "trustees";
	protected $guarded = array();

	public function trustee()
	{
		return $this->belongsTo("Member", "trustee_id");
	}

	// TODO/FIXME: trustees are not trustees anymore.
	public static function make($member_id, $trustee_id, $creator_id)
	{
		// Trustee is a trustee for a member.
		$first_trustee = self::where("member_id", "=", $member_id)->where("trustee_id", "=", $trustee_id)->count();

		if ($first_trustee == 0)
		{
			$item = self::insert(array(
				"member_id" => $member_id,
				"trustee_id" => $trustee_id
			));
		}

		// Creator is a trustee for a member.
		$second_trustee = self::where("member_id", "=", $member_id)->where("trustee_id", "=", $creator_id)->count();

		if ($second_trustee == 0)
		{
			self::insert(array(
				"member_id" => $member_id,
				"trustee_id" => $creator_id
			));
		}
	}
}
