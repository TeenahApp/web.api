<?php

class Action extends Eloquent {

	protected $table = "actions";
	protected $guarded = array();

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}

	public function affected()
	{
		return $this->belongsTo("Member", "affected_member_id");
	}

	// Returns either true of false.
	// TODO: Check if the affected id does exist.
	public static function make($action, $area, $affected_id, $content = null)
	{
		// Get the user that is logged in.
		$user = User::current();

		if (is_null($user))
		{
			return false;
		}

		$validator = Validator::make(
			array(
				"area" => $area,
				"affected_id" => $affected_id,
				"action" => $action
			),
			array(
				"area" => "required|in:member,event,media,member_comment,event_comment,media_comment",
				"affected_id" => "required|numeric",
				"action" => "in:view,comment,like,flag"
			)
		);

		if ($validator->fails())
		{
			return false;
		}

		// Remove the (s) from area.
		$oneaction = null;

		// The member can do anything else many times but these actions cannot.
		if (in_array($action, array("like", "flag")))
		{
			// Check if the action has been added before.
			$oneaction = Action::where("area", "=", $area)->where("action", "=", $action)->where("affected_id", "=", $affected_id)->first();
		}

		if (!is_null($oneaction))
		{
			return false;
		}

		// Create it.
		$oneaction = Action::create(array(
			"area" => $area,
			"action" => $action,
			"affected_id" => $affected_id,
			"content" => $content,
			"created_by" => $user->member_id
		));

		// Done.
		return true;
	}

	// To make it easy to remember.
	public static function like($area, $affected_id)
	{
		return self::make("like", $area, $affected_id);
	}

	public static function view($area, $affected_id)
	{
		return self::make("view", $area, $affected_id);
	}

	public static function flag($area, $affected_id)
	{
		return self::make("flag", $area, $affected_id);
	}

	public static function comment($area, $affected_id, $comment)
	{
		return self::make("comment", $area, $affected_id, $comment);
	}

	// Create a method to calculate actions.
	public static function calculate($action, $area, $affected_id)
	{
		return Action::where("area", "=", $area)->where("action", "=", $action)->where("affected_id", "=", $affected_id)->count();
	}

	// Create a method to get actions.
	public static function get($action, $area, $affected_id)
	{
		return Action::with("creator")->where("area", "=", $area)->where("action", "=", $action)->where("affected_id", "=", $affected_id)->get();
	}
}
