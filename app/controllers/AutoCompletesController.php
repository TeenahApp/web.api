<?php

class AutoCompletesController extends \Controller {

	// For companies.
	public function companies($query)
	{
		$validator = Validator::make(
			array(
				"query" => $query
			),
			array(
				"query" => "required"
			)
		);

		// Check if the validator fails.
		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// TODO: It should not be hard-coded.
		$companies = JobCompany::where("name", "LIKE", "%$query%")->limit(10)->get()->toArray();

		return Response::json($companies, 200);
	}

	// For majors.
	public function majors($query)
	{
		$validator = Validator::make(
			array(
				"query" => $query
			),
			array(
				"query" => "required"
			)
		);

		// Check if the validator fails.
		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// TODO: It should not be hard-coded.
		$majors = EducationMajor::where("name", "LIKE", "%$query%")->limit(10)->get()->toArray();

		return Response::json($majors, 200);
	}

}