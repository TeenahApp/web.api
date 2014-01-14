<?php

class MemberEducationsController extends \Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($id)
	{
		// Check if the id does exist.
		$member = Member::where("id", "=", $id)->first();

		if (is_null($member))
		{
			return Response::json(array(
				"message" => "The chosen member has not been found."
			), 404);
		}

		$user = User::current();

		if (!Member::canUseResource($user->member_id, $member->id))
		{
			return Response::json(array(
				"message" => "Not authroized to use this resource."
			), 403);
		}

		// List all educations for a member.
		return MemberEducation::with("major")->where("member_id", "=", $id)->get();
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
	public function store($id)
	{
		$user = User::current();

		if (!Member::canUseResource($user->member_id, $id))
		{
			return Response::json(array(
				"message" => "Not authroized to use this resource."
			), 403);
		}

		$validator = Validator::make(
			array(
				"degree" => Input::get("degree"),
				"start_year" => Input::get("start_year"),
				"finish_year" => Input::get("finish_year"),
				"status" => Input::get("status")
			),
			array(
				"degree" => "required|in:none,elementary,intermediate,secondary,diploma,licentiate,bachelor,master,doctorate",
				"start_year" => "numeric",
				"finish_year" => "numeric",
				"status" => "in:ongoing,finished,pending,dropped"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		$major = Input::get("major");
		$finish_year = Input::get("finish_year");

		// Check if the major is not empty.
		if (!empty($major))
		{
			$found_major = EducationMajor::where("name", "=", $major)->first();

			if (is_null($found_major))
			{
				// Create a major.
				$found_major = EducationMajor::create(array("name" => $major));
				$major_id = $found_major->id;
			}
			else
			{
				$major_id = $found_major->id;
			}
		}
		else
		{
			$major_id = null;
		}

		// If finish year is there, then status is finished.
		$status = "ongoing";

		if (!empty($finish_year))
		{
			$status = "finished";
		}

		// TODO: Should check if the education already exists.
		$education = MemberEducation::create(
			array(
				"member_id" => $id,
				"degree" => Input::get("degree"),
				"major_id" => $major_id,
				"start_year" => Input::get("start_year"),
				"finish_year" => $finish_year,
				"status" => $status
			)
		);

		// Done.
		return Response::json(array(
			"message" => "The education has been created.",
			"degree" => $education->degree,
			"major_id" => $major_id,
			"start_year" => $education->start_year,
			"finish_year" => $education->finish_year,
			"status" => $education->status
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