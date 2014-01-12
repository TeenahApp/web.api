<?php

class MemberRelationsController extends \Controller {

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
	public function store($member_a)
	{
		// Get the first member to relate from.
		$member_a = Member::where("id", "=", $member_a)->first();

		if (is_null($member_a))
		{
			return Response::json(array(
				"message" => "The member that is chosen cannot be found."
			), 404);
		}

		// Get the correct relationships for the first member.
		$correct_relations = implode(",", MemberRelation::correctGenderRelations($member_a->gender));

		// Check if the (logged in) member is able to upload a photo for the chosen member.
		$logged_in_user = User::current();

		if (!Member::canUseResource($logged_in_user->member_id, $member_a->id))
		{
			return Response::json(array(
				"message" => "Not authorized to access this resource."
			), 403);
		}

		$validator = Validator::make(
			array(
				"is_alive" => Input::get("is_alive"),
				"name" => Input::get("name"),
				"dob" => Input::get("dob"),
				"mobile" => Input::get("mobile"),
				"relation" => Input::get("relation"),
				"is_root" => Input::get("is_root")
			),
			array(
				"is_alive" => "required|in:0,1",
				"name" => "required",
				"dob" => "date",
				"mobile" => "numeric",
				"relation" => "required|in:$correct_relations" // TODO: Specify only correct relations.
				"is_root" => "required|in:0,1"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		$name = Member::normalize(Input::get("name"));
		$mobile = Input::get("mobile");

		if (is_null($name))
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		$member_b = null;

		// Find the member (B), or create.
		if (!empty($mobile))
		{
			// Search for a member with the specified mobile.
			$member_b = Member::where("mobile", "=", $mobile)->first();
		}

		if (is_null($member_b))
		{
			$member_b = Member::create(
				array(
					"is_alive" => Input::get("is_alive"),
					"name" => $name,
					"dob" => Input::get("dob"),
					"mobile" => $mobile
				)
			);

			// TODO: Send SMS asking new member to download app.
		}

		// TODO: Check if the related member is a root.
		if (Input::get("is_root") == 1)
		{
			// TODO: Please fix this.
		}

		// Make a relationship between two members.
		$result = MemberRelation::make($member_a, $member_b, Input::get("relation"));

		return Response::json(array(
			"message" => "Relationship has been created successfully.",
			"a-to-b" => $result["a-to-b"],
			"b-to-a" => $result["b-to-a"]
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

}