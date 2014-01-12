<?php

class ActionsController extends \Controller {

	//actions/{area}/{id}/{action}

	public function store($area, $affected_id, $action)
	{
		// Get the user that is logged in.
		$user = User::current();

		$validator = Validator::make(
			array(
				"area" => $area,
				"affected_id" => $affected_id,
				"action" => $action
			),
			array(
				"area" => "required|in:members,events,media,member_comments,event_comments,media_comments",
				"affected_id" => "required|numeric",
				"action" => "in:view,comment,like,flag"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Remove the (s) from area.
		$area = substr($area, 0, -1);
		$oneaction = null;

		if (in_array($action, array("like", "flag")))
		{
			// Check if the action has been added before.
			$oneaction = Action::where("area", "=", $area)->where("action", "=", $action)->where("affected_id", "=", $affected_id)->first();
		}

		if (!is_null($oneaction))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Create it.
		$oneaction = Action::create(array(
			"area" => $area,
			"action" => $action,
			"affected_id" => $affected_id,
			"content" => Input::get("content"),
			"created_by" => $user->member_id
		));

		// Done.
		return Response::json("", 204);
	}

	public function show($area, $affected_id, $action)
	{
		
	}

	public function destroy($area, $affected_id, $action)
	{

	}
}