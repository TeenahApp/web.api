<?php

class EventsController extends \Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		$user = User::current();

		// title, start_datetime, finish_datetime, location, latitude, longtitude, circles
		$validator = Validator::make(
			array(
				"title" => Input::get("title"),
				"start_datetime" => Input::get("start_datetime"),
				"finish_datetime" => Input::get("finish_datetime"),
				"location" => Input::get("location"),
				"latitude" => Input::get("latitude"),
				"longtitude" => Input::get("longtitude"),
				"circles" => Input::get("circles")
			),
			array(
				"title" => "required",
				"start_datetime" => "required|date",
				"finish_datetime" => "required|date",
				"location" => "required",
				"latitude" => "regex:/\d+\.\d+/",
				"longtitude" => "regex:/\d+\.\d+/",
				"circles" => "required|regex:/\[(\d)+(,\d+)*\]/"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		$circles = explode(",", substr(Input::get("circles"), 1, -1));

		// Check if the member is in the circles.
		$member_found_in_circles = MemberCircle::where("member_id", "=", $user->member_id)->whereIn("circle_id", $circles)->count();

		if ($member_found_in_circles == 0)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Insert the event.
		$event = TEvent::create(array(
			"title" => Input::get("title"),
			"start_datetime" => Input::get("start_datetime"),
			"finish_datetime" => Input::get("finish_datetime"),
			"location" => Input::get("location"),
			"latitude" => Input::get("latitude"),
			"longtitude" => Input::get("longtitude"),
			"created_by" => $user->member_id
		));

		// Invite circle(s) members.
		foreach ($circles as $circle_id)
		{
			$circle_members = Circle::find($circle_id)->members()->get();

			// TODO: Add a new message that is different, for inviting members.

			// members invite members of this circle.
			foreach ($circle_members as $circle_member)
			{
				CircleEventMember::create(array(
					"circle_id" => $circle_id,
					"event_id" => $event->id,
					"member_id" => $circle_member->id
				));

				// TODO: Notify the members by pushing notifications.
			}
		}

		// Done.
		return Response::json(array(
			"message" => "The event has been created successfully.",
			"event_id" => $event->id
		), 201);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
		$user = User::current();

		$validator = Validator::make(
			array(
				"id" => $id
			),
			array(
				"id" => "required|integer"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Check if the member is invited.
		$member_circles = array_fetch($user->member->circles()->get(), "id");
		$member_found_in_circles = CircleEventMember::where("event_id", "=", $id)->where("member_id", "=", $user->member_id)->whereIn("circle_id", $member_circles)->count();

		if ($member_found_in_circles == 0)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// TODO: Get the circle(s) that is/are invited to the event with the member(s).
		$event = TEvent::with("creator")->with("members")->with("medias")->find($id);
		//$cem_circles = CircleEventMember::where("event_id", "=", $event->id)->whereIn("circle_id", $member_circles)->groupBy("circle_id")->with("circle")->get();//, "circle");
		//dd($cem_circles);

		// Make an action for the logged in user (member); specifically "view".
		Action::view("event", $event->id);

		// TODO: Get the decisions.
		// TODO: Get the likes, and comments.

		// TODO: Fill the circles.
		/*
		return Response::json(array(
				"id" => $event->id,
				"title" => $event->title,
				"start_datetime" => $event->start_datetime,
				"finish_datetime" => $event->finish_datetime,
				"location" => $event->location,
				"latitude" => $event->latitude,
				"longtitude" => $event->longtitude,
				"creator" => array(
					"id" => $event->creator->id,
					"name" => $event->creator->name,
					"mobile" => $event->creator->mobile
				),
				"circles" => array()
		), 200);
		*/

		return $event;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
		return "hello world";
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
		$user = User::current();

		// Check if the event does exist.
		$event = TEvent::find($id);

		if (is_null($event))
		{
			return Response::json(array(
				"message" => "The event is not found."
			), 404);
		}

		// Check if the logged in user (member) is the creator of the event.
		if ($event->created_by != $user->member_id)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Everything is fine and dandy.
		// TODO: Delete every related row to $event.
		$event->delete();

		return Response::json("", 204);
	}

	public function decide($id, $decision)
	{
		// TODO: Make a decision for the member about the event.
		$user = User::current();

		$validator = Validator::make(
			array(
				"id" => $id,
				"decision" => $decision
			),
			array(
				"id" => "required|numeric",
				"decision" => "required|in:willcome,apologize"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Check if the member is invited.
		$invitation_found = CircleEventMember::where("event_id", "=", $id)->where("member_id", "=", $user->member_id)->where("decision", "=", "notyet")->first();

		if (is_null($invitation_found))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// TODO: Check if the event is out-dated.
		// Make the decision.
		$invitation_found->update(array(
			"decision" => $decision
		));

		// Done.
		return Response::json("", 204);
	}

	public function showDecision($id)
	{
		// TODO: Make a decision for the member about the event.
		$user = User::current();

		$validator = Validator::make(
			array(
				"id" => $id,
			),
			array(
				"id" => "required|numeric",
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Check if the member is invited.
		$invitation_found = CircleEventMember::where("event_id", "=", $id)->where("member_id", "=", $user->member_id)->first();

		if (is_null($invitation_found))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Done.
		return Response::json(array(
			"decision" => $invitation_found->decision
		), 200);
	}

}