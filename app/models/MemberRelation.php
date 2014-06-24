<?php

class MemberRelation extends Eloquent {

	protected $table = "member_relations";
	protected $guarded = array();

	public function firstMember()
	{
		return $this->belongsTo("Member", "member_a");
	}

	public function secondMember()
	{
		return $this->belongsTo("Member", "member_b");
	}

	public static function correctGenderRelations($gender)
	{
		$correctRelations = array(
			"father", "stepfather", "father-in-law", "mother", "stepmother", "mother-in-law",
			"sister", "brother", "son", "stepson", "daughter", "stepdaughter", "son-in-law", "daughter-in-law"
		);

		if ($gender == "male")
		{
			 $correctRelations []= "wife";
		}
		else
		{
			$correctRelations []= "husband";
		}

		return $correctRelations;
	}

	// Make a relationship between two members.
	public static function make($member_a, $member_b, $relation)
	{
		// Get the logged in user.
		$user = User::current();

		// $member_b is a $relation to $member_a.
		$relationOne = self::where("member_a", "=", $member_b->id)->where("member_b", "=", $member_a->id)->first();

		if (is_null($relationOne))
		{
			$relationOne = self::create(array(
				"member_a" => $member_b->id,
				"member_b" => $member_a->id,
				"relationship" => $relation
			));
		}

		// Get the inverse relation between $member_b and $member_a.
		$inverse = self::inverse($relation, $member_a->gender);

		// $member_a is an inverse $relation to $member_b.
		$relationTwo = self::where("member_a", "=", $member_a->id)->where("member_b", "=", $member_b->id)->first();
		
		if (is_null($relationTwo))
		{
			$relationTwo = self::create(array(
				"member_a" => $member_a->id,
				"member_b" => $member_b->id,
				"relationship" => $inverse
			));
		}

		// Update the fullname for one or both of members.
		if (in_array($relation, array("father", "son", "daughter")))
		{
			if ($relation == "father")
			{
				// Get the tribe id.
				$tribe_id = Member::getTribeId($member_b->id);
			}
			else
			{
				// Get the tribe id.
				$tribe_id = Member::getTribeId($member_a->id);
			}

			// Update the fullname of the tribe and the members.
			Member::updateTribeFullnames($tribe_id);
		}

		// Make/Update the trustees.
		Trustee::make($member_b->id, $member_a->id, $user->member_id);

		// Done.
		return array(
			"a-to-b" => $inverse,
			"b-to-a" => $relation
		);
	}

	// Get the inverse relationship.
	public static function inverse($relation, $gender_a)
	{
		if ($gender_a == "male")
		{
			$child = "son";
			$parent = "father";
			$sibling = "brother";
		}
		else
		{
			$child = "daughter";
			$parent = "mother";
			$sibling = "sister";
		}

		// Do some checks to decide best inverse relation.
		if ($relation == "husband")
		{
			return "wife";
		}

		if ($relation == "wife")
		{
			return "husband";
		}

		if ($relation == "father" || $relation == "mother")
		{
			return $child;
		}
		
		if ($relation == "stepfather" || $relation == "stepmother")
		{
			return "step" . $child;
		}

		if ($relation == "father-in-law" || $relation == "mother-in-law")
		{
			return $child . "-in-law";
		}

		if ($relation == "brother" || $relation == "sister")
		{
			return $sibling;
		}

		if ($relation == "son" || $relation == "daughter")
		{
			return $parent;
		}

		if ($relation == "stepson" || $relation == "stepdaughter")
		{
			return "step" . $parent;
		}
	}
}