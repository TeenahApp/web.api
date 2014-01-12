<?php

class MemberJobsController extends \Controller {

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

		// List all jobs for a member.
		return MemberJob::with("company")->where("member_id", "=", $id)->get();
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

		// title, company, start_year, finish_year, status

		$validator = Validator::make(
			array(
				"title" => Input::get("title"),
				"start_year" => Input::get("start_year"),
				"finish_year" => Input::get("finish_year"),
				"status" => Input::get("status")
			),
			array(
				"title" => "required",
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

		$company = Input::get("company");
		$finish_year = Input::get("finish_year");

		// Check if the company is not empty.
		if (!empty($company))
		{
			$found_company = JobCompany::where("name", "=", $company)->first();

			if (is_null($found_company))
			{
				// Create a company.
				$found_company = JobCompany::create(array("name" => $company));
				$company_id = $found_company->id;
			}
			else
			{
				$company_id = $found_company->id;
			}
		}
		else
		{
			$company_id = null;
		}

		// If finish year is there, then status is finished.
		$status = "ongoing";

		if (!empty($finish_year))
		{
			$status = "finished";
		}

		// TODO: Should check if the job already exists.
		$job = MemberJob::create(
			array(
				"member_id" => $id,
				"title" => Input::get("title"),
				"company_id" => $company_id,
				"start_year" => Input::get("start_year"),
				"finish_year" => $finish_year,
				"status" => $status
			)
		);

		// Done.
		return Response::json(array(
			"message" => "The job has been created.",
			"title" => $job->title,
			"company_id" => $company_id,
			"start_year" => $job->start_year,
			"finish_year" => $job->finish_year,
			"status" => $job->status
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