<?php

class CirclesController extends \Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = User::current();

		// Get the count of circles for a member.
		$circles_count = $user->member->circles()->count();

		if ($circles_count == 0)
		{
			// Detect circles depending on relations.
			$relations = $user->member->outRelations()->with(
				array("secondmember" => function($query){
					$query->where("mobile", "!=", "");
				})
			)->get();

			if (count($relations) > 0)
			{
				// Check if one of the member relations is in a circle.
				// TODO: This should go to MemberCircle model.
				$best_circle = DB::table("member_circles")
					->select("circle_id", DB::raw("COUNT(*)"))
					->join("member_relations", function($join) use ($user){
						$join->on("member_circles.member_id", "=", "member_relations.member_b")
							->where("member_a", "=", $user->member_id)
							->where("active", "=", "1");
					})
					->groupBy("circle_id")
					->orderBy(DB::raw("COUNT(*)"), "DESC")
					->take(1)
					->first();

				if (is_null($best_circle))
				{
					// Create a new circle containing this member and relations.
					$best_circle = Circle::create(array(
						"name" => str_random(20),
						"created_by" => $user->member_id
					));
				}
				
				// Add this member to the best circle.
				MemberCircle::create(array(
					"member_id" => $user->member_id,
					"circle_id" => $best_circle->id
				));

				// Add relations to the best circle.
				foreach ($relations as $relation)
				{
					MemberCircle::create(array(
						"member_id" => $relation->member_b,
						"circle_id" => $best_circle->id
					));
				}

				// Update and save.
				$best_circle->members_count	= 1 + count($relations);
				$best_circle->save();
			}
		}

		// Get the circles for a member.
		$circles = $user->member->circles()->where("status", "=", "active")->get();

		// Done.
		return Response::json($circles, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$user = User::current();

		$validator = Validator::make(
			array(
				"name" => Input::get("name"),
				"members" => Input::get("members"),
			),
			array(
				"name" => "required",
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

		// TODO: Check if the members do exist in the members table.
		$real_members_count = Member::whereIn("id", $members)->where("id", "!=", $user->member_id)->count();

		if (count($members) != $real_members_count)
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Everything is so beautiful.
		// Add the circle.
		$circle = Circle::create(array(
			"name" => Input::get("name"),
			"created_by" => $user->member_id
		));

		// Add the creator to that circle.
		MemberCircle::create(array(
			"member_id" => $user->member_id,
			"circle_id" => $circle->id
		));

		// Add members to that circle.
		foreach ($members as $member)
		{
			MemberCircle::create(array(
				"member_id" => $member,
				"circle_id" => $circle->id
			));
		}

		// Update and save.
		$circle->members_count	= 1 + count($members);
		$circle->save();

		// Done.
		return Response::json(array(
			"message" => "The circle has been created successfully.",
			"id" => $circle->id,
			"name" => $circle->name
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
		// TODO: There is another method gets the circle members.
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
		$user = User::current();

		// Check if the circle does exist.
		$circle = Circle::where("id", "=", $id)->where("created_by", "=", $user->member_id)->first();

		if (is_null($circle))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		$validator = Validator::make(
			array(
				"name" => Input::get("name"),
			),
			array(
				"name" => "required"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Update the circle.
		$circle->update(array(
			"name" => Input::get("name")
		));

		// Done.
		return Response::json(array(
			"message" => "Circle has been updated successfully."
		), 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		// TODO: This is a controversial method.
	}

	public function stats($id)
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

		// Get the circle members.
		$circle_members = array_fetch(MemberCircle::where("circle_id", "=", $id)->select("member_id")->get()->toArray(), "member_id");

		return Response::json(
			Circle::stats($circle_members)
		, 200);
	}

}