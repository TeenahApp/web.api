<?php

class MemberCirclesController extends \Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($id)
	{
		//
		$user = User::current();

		// Check if the circle does exist.
		$member_circles_count = MemberCircle::where("circle_id", "=", $id)->where("member_id", "=", $user->member_id)->count();

		if ($member_circles_count == 0)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Get the members of the circle.
		// TODO: Check if this works if empty.
		return Response::json(Circle::find($id)->members()->get(), 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($id)
	{
		//
		$user = User::current();

		// Check if the circle does exist.
		$member_circles_count = MemberCircle::where("circle_id", "=", $id)->where("member_id", "=", $user->member_id)->count();

		if ($member_circles_count == 0)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		$validator = Validator::make(
			array(
				"members" => Input::get("members"),
			),
			array(
				"members" => "required|regex:/\[(\d)+(,\d+)*\]/"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Extract members from the given list.
		$members = explode(",", substr(Input::get("members"), 1, -1));

		// Check if the members do exist in the members table.
		$real_members_count = Member::whereIn("id", $members)->where("id", "!=", $user->member_id)->count();

		if (count($members) != $real_members_count)
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Check if one of the members does exist in the circle before.
		$existing_members_count = MemberCircle::where("circle_id", "=", $id)->whereIn("member_id", $members)->count();

		if ($existing_members_count > 0)
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Everything is okay.
		foreach ($members as $member)
		{
			MemberCircle::create(array(
				"member_id" => $member,
				"circle_id" => $id
			));
		}

		// Done.
		return Response::json(array(
			"message" => "Members have been added to the circle successfully."
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
	}


	public function leave($id)
	{
		//
		$user = User::current();

		// Check if the member is in the circle.
		$member_circles_count = MemberCircle::where("circle_id", "=", $id)->where("member_id", "=", $user->member_id)->count();

		if ($member_circles_count == 0)
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Everything is okay, let the member leaves the circle.
		//MemberCircle::where("circle_id", "=", $id)->where("member_id", "=", $user->member_id)->update(array("status" => "blocked"));
		MemberCircle::where("circle_id", "=", $id)->where("member_id", "=", $user->member_id)->delete();

		// Done.
		return Response::json("", 204);
	}

}