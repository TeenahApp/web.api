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

		// Check if the action has been added before.
		$oneaction = Action::where();
	}

	public function show($area, $affected_id, $action)
	{

	}

	public function destroy($area, $affected_id, $action)
	{

	}
}