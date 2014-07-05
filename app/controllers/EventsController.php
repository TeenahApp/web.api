<?php

class EventsController extends \Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($circle_id)
	{
		//
		$user = User::current();

		// Check if the member is a member of the circle.
		$memberized = MemberCircle::where("circle_id", "=", $circle_id)->where("member_id", "=", $user->member_id)->first();

		if (is_null($memberized))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		$events = CircleEventMember::with("event")->where("circle_id", "=", $circle_id)->where("member_id", "=", $user->member_id)->get();
		
		// Done.
		return array_fetch($events->toArray(), "event");
	}

	public function get()
	{
		//
		$user = User::current();

		$member_circles = array_fetch($user->member->circles()->get(), "id");

		if (count($member_circles) == 0)
		{
			return Response::json(array(
				"message" => "You must at least sign up in one or more circle."
			), 403);
		}

		$events = CircleEventMember::with("event")->where("member_id", "=", $user->member_id)->whereIn("circle_id", $member_circles)->get();

		// Done.
		return array_fetch($events->toArray(), "event");
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

		// title, start_datetime, finish_datetime, location, latitude, longitude, circles
		$validator = Validator::make(
			array(
				"title" => Input::get("title"),
				"start_datetime" => Input::get("start_datetime"),
				"finish_datetime" => Input::get("finish_datetime"),
				"location" => Input::get("location"),
				"latitude" => Input::get("latitude"),
				"longitude" => Input::get("longitude"),
				"circles" => Input::get("circles")
			),
			array(
				"title" => "required",
				"start_datetime" => "required|date",
				"finish_datetime" => "required|date",
				"location" => "required",
				"latitude" => "regex:/\d+\.\d+/",
				"longitude" => "regex:/\d+\.\d+/",
				"circles" => "required|regex:/\[(\d)+(,\d+)*\]/"
			)
		);

		if ($validator->fails())
		{
			echo "Happend";
			var_dump($validator->failed());

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
			"longitude" => Input::get("longitude"),
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

		// Check if the current member liked the comment.
		$member_likes_count = Action::where("area", "=", "event")->where("action", "=", "like")->where("affected_id", "=", $event->id)->where("created_by", "=", $user->member_id)->count();
		$event->has_liked = ($member_likes_count > 0) ? 1 : 0;

		// Make an action for the logged in user (member); specifically "view".
		Action::view("event", $event->id);

		// TODO: Fill the circles.

		//$event->likes_count = 4;
		return $event;//->likes();
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// Only the creator of the event is able to update it.
		$user = User::current();

		// 
		$validator = Validator::make(
			array(
				"title" => Input::get("title"),
				"start_datetime" => Input::get("start_datetime"),
				"finish_datetime" => Input::get("finish_datetime"),
				"location" => Input::get("location"),
				"latitude" => Input::get("latitude"),
				"longitude" => Input::get("longitude")
			),
			array(
				"title" => "required",
				"start_datetime" => "required|date",
				"finish_datetime" => "required|date",
				"location" => "required",
				"latitude" => "regex:/\d+\.\d+/",
				"longitude" => "regex:/\d+\.\d+/"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

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

		$event->update(array(
			"title" => Input::get("title"),
			"start_datetime" => Input::get("start_datetime"),
			"finish_datetime" => Input::get("finish_datetime"),
			"location" => Input::get("location"),
			"latitude" => Input::get("latitude"),
			"longitude" => Input::get("longitude")
		));

		// Done.
		return Response::json("", 204);
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
		// Delete every related row to $event.
		$event->delete();

		return Response::json("", 204);
	}

	public function decide($id, $decision)
	{
		// Make a decision for the member about the event.
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

	public function getComments($event_id)
	{
		$user = User::current();

		// TODO: Check if the user is able to access this resource.

		$comments = Action::get("comment", "event", $event_id);

		// Get the likes of the event.
		$comments->each(function($comment) use ($user)
		{
			$likes_count = Action::calculate("like", "event_comment", $comment->id);

			// Check if the current member liked the comment.
			$member_likes_count = Action::where("area", "=", "event_comment")->where("action", "=", "like")->where("affected_id", "=", $comment->id)->where("created_by", "=", $user->member_id)->count();

			$comment->likes_count = $likes_count;
			$comment->has_liked = ($member_likes_count > 0) ? 1 : 0;
		});

		return $comments;
	}

}
