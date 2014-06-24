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
	 * TODO: Check if the related member has the same mobile number as a test.
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
				"dod" => Input::get("dod"),
				"mobile" => Input::get("mobile"),
				"relation" => Input::get("relation"),
			),
			array(
				"is_alive" => "required|in:0,1",
				"name" => "required",
				"dob" => "date",
				"dod" => "date",
				"mobile" => "numeric",
				"relation" => "required|in:$correct_relations", // TODO: Specify only correct relations.
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
		$second_relation = Input::get("second_relation");

		// Make dob and dod null.
		$dob = Input::get("dob") ? : null;
		$dod = Input::get("dod") ? : null;
		$second_relation = Input::get("second_relation") ? : null;

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
			// Specify the gender.
			$gender = "male";

			$femaleRelations = array(
				"mother", "stepmother", "mother-in-law", "sister",
				"daughter", "stepdaughter", "daughter-in-law"
			);

			if (in_array(Input::get("relation"), $femaleRelations))
			{
				$gender = "female";
			}

			$member_b = Member::create(
				array(
					"is_alive" => Input::get("is_alive"),
					"name" => $name,
					"dob" => $dob,
					"dod" => $dod,
					"mobile" => $mobile,
					"gender" => $gender
				)
			);

			if (!empty($mobile))
			{
				// TODO: Use queuing would be better.
				// Send SMS asking new member to download app.
				$sms = Nexmo::SMS(Config::get("nexmo::api_key"), Config::get("nexmo::api_secret"), Config::get("nexmo::sender"));
				$text = "السلام عليكم، حيّاك الله،\n\n" . $logged_in_user->member->name . " يدعوك لتحميل تطبيق تينه و الانضمام إلى العائلة.";

				// Send the message.
				$sms->send($mobile, $text);
			}
		}

		// Check the second relations.
		if (Input::get("relation") == "father" && $second_relation == "root")
		{
			Member::updateTribeIds($member_b->id, $member_b->id);
		}

		if (Input::get("relation") == "father" || Input::get("relation") == "mother")
		{
			// Sons of them are brother of member_a, daughters of them also.
			$son_relations = MemberRelation::where("member_b", "=", $member_b->id)->where("relationship", "=", "son")->where("member_a", "<>", $member_a->id)->get();
			$daughter_relations = MemberRelation::where("member_b", "=", $member_b->id)->where("relationship", "=", "daughter")->where("member_a", "<>", $member_a->id)->get();

			$me_relation = ($member_a->gender == "male") ? "brother" : "sister";

			foreach ($son_relation as $son_relations)
			{
				// A is a brother to B.
				$brother = new Member();
				$brother->id = $son_relation->member_a;
				$brother->gender = "male";

				MemberRelation::make($member_a, $brother, "brother");
			}

			foreach ($daughter_relation as $daughter_relations)
			{
				// A is a sister to B.
				$sister = new Member();
				$sister->id = $daughter_relation->member_a;
				$sister->gender = "female";

				MemberRelation::make($member_a, $sister, "sister");
			}
		}

		if (Input::get("relation") == "brother" || Input::get("relation") == "sister")
		{
			// MOTHER
			// Get the father of member A, B.
			$a_mother = MemberRelation::where("member_b", "=", $member_a->id)->where("relationship", "=", "mother")->first();
			$b_mother = MemberRelation::where("member_b", "=", $member_b->id)->where("relationship", "=", "mother")->first();

			$best_mother = null;

			if (($a_mother == null && $b_mother == null) || ($a_mother != null && $b_mother != null));
			else
			{
				// Get the best father A, B by mobile number.
				if ($b_mother == null)
				{
					$best_mother = Member::where("id", "=", $a_mother->member_a)->first();
				}
				else
				{
					$best_mother = Member::where("id", "=", $b_mother->member_a)->first();
				}
			}

			// FATHER.
			// Get the father of member A, B.
			$a_father = MemberRelation::where("member_b", "=", $member_a->id)->where("relationship", "=", "father")->first();
			$b_father = MemberRelation::where("member_b", "=", $member_b->id)->where("relationship", "=", "father")->first();

			$best_father = null;

			if (($a_father == null && $b_father == null) || ($a_father != null && $b_father != null));
			else
			{
				// Get the best father A, B.
				if ($b_father == null)
				{
					$best_father = Member::where("id", "=", $a_father->member_a)->first();
				}
				else
				{
					$best_father = Member::where("id", "=", $b_father->member_a)->first();
				}
			}

			if (($second_relation == "full" || $second_relation == "mother") && $best_mother != null)
			{
				// Add one relation to mother.

				// A, Son/Daughter of, Y.
				MemberRelation::make($best_mother, $member_a, ($member_a->gender == "male") ? "son" : "daughter");
				// B, Son/Daughter of, Y.
				MemberRelation::make($best_mother, $member_b, ($member_b->gender == "male") ? "son" : "daughter");
			}

			if (($second_relation == "full" || $second_relation == "father") && $best_father != null)
			{
				// Add one relation to father.

				// A, Son/Daughter of, X.
				MemberRelation::make($best_father, $member_a, ($member_a->gender == "male") ? "son" : "daughter");
				// B, Son/Daughter of, X.
				MemberRelation::make($best_father, $member_b, ($member_b->gender == "male") ? "son" : "daughter");
			}
		}

		if (Input::get("relation") == "son" || Input::get("relation") == "daughter")
		{
			// Member A is a male, get the mother of the child and marry her.
			if ($member_a->gender == "male")
			{
				// Get the mother of the child.
				$b_mother = MemberRelation::where("member_b", "=", $member_b->id)->where("relationship", "=", "mother")->first();

				if ($b_mother)
				{
					$wife = new Member();
					$wife->id = $b_mother->member_a;
					$wife->gender = "female";

					MemberRelation::make($wife, $member_a, "husband");
				}
			}
			// Member A is a female, get the father of the child and marry him.
			else
			{
				// Get the father of the child.
				$b_father = MemberRelation::where("member_b", "=", $member_b->id)->where("relationship", "=", "father")->first();

				if ($b_father)
				{
					$husband = new Member();
					$husband->id = $b_father->member_a;
					$husband->gender = "male";

					MemberRelation::make($husband, $member_a, "wife");
				}
			}
		}

		if (Input::get("relation") == "wife" || Input::get("relation") == "husband")
		{
			// TODO: Their children are mine. Phase #2.
		}

		// Make a relationship between two members.
		$result = MemberRelation::make($member_a, $member_b, Input::get("relation"));

		return Response::json(array(
			"message" => "Relationship has been created successfully.",
			"a-to-b" => $result["a-to-b"],
			"b-to-a" => $result["b-to-a"],
			"member_a" => $member_a->id,
			"member_b" => $member_b->id,
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
	public function destroy($member_a)
	{

		// Get the first member to relate from.
		$member_a = Member::where("id", "=", $member_a)->first();

		if (is_null($member_a))
		{
			return Response::json(array(
				"message" => "The member that is chosen cannot be found."
			), 404);
		}

		// Check if the (logged in) member is able to upload a photo for the chosen member.
		$logged_in_user = User::current();

		if (!Member::canUseResource($logged_in_user->member_id, $member_a->id))
		{
			return Response::json(array(
				"message" => "Not authorized to access this resource."
			), 403);
		}

		// Get the member_b to decide.
		$member_b = Member::find(Input::get("member_b"));

		// Check if the member does exist.
		if (is_null($member_b))
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Delete the relation from (a) to (b); and vice versa.
		// I.
		MemberRelation::where("member_a", "=", $member_a->id)->where("member_b", "=", $member_b->id)->delete();
		Trustee::where("member_a", "=", $member_a->id)->where("member_b", "=", $member_b->id)->delete();

		// II.
		MemberRelation::where("member_a", "=", $member_b->id)->where("member_b", "=", $member_a->id)->delete();
		Trustee::where("member_a", "=", $member_b->id)->where("member_b", "=", $member_a->id)->delete();

		// Done.
		return Response::json("", 204);
	}
}
