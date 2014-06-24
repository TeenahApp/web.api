<?php

class Member extends Eloquent {

	protected $table = "members";
	protected $guarded = array();
	protected $appends = array("social_medias", "updates_count", "views_count", "likes_count", "comments_count", "medias_count");

	public function user()
	{
		return $this->hasOne("User", "mobile", "mobile");
	}

	public function circles()
	{
		// Set the member right.
		$member_id = $this->id;

		return DB::table("circles")->select("circles.*")->join("member_circles", function($join) use ($member_id){
			$join->on("circles.id", "=", "member_circles.circle_id")
				->where("member_id", "=", $member_id);
		});
	}

	public function createdActions()
	{
		return $this->hasMany("Action", "created_by");
	}

	public function affectedActions()
	{
		return $this->hasMany("Action", "affected_member_id");
	}

	public function educations()
	{
		return $this->hasMany("MemberEducation")->with("major");
	}

	// TODO: Check the foregin keys.
	// TODO: Check if this method is needed.
	public function educationMajors()
	{
		return $this->hasManyThrough("EducationMajor", "MemberEducation");
	}

	public function jobs()
	{
		return $this->hasMany("MemberJob")->with("company");
	}

	// TODO: Check if this method is needed too.
	// OPINION: There is no need to complicate issues more.
	public function jobCompanies()
	{
		return $this->hasManyThrough("JobCompany", "MemberJob");
	}

	public function privacies()
	{
		return $this->hasManyThrough("Privacy", "MemberPrivacy");
	}

	// TODO: Find a fine name.
	public function outRelations()
	{
		return $this->hasMany( "MemberRelation", "member_a");
	}

	// TODO: Find a comprehensive name.
	public function inRelations()
	{
		return $this->hasMany("MemberRelation", "member_b");
	}

	public function socialMedias()
	{
		return $this->hasManyThrough("SocialMedia", "MemberSocialMedia");
	}

	public function trustees()
	{
		return $this->hasMany("Trustee");
	}

	public function getSocialMediasAttribute()
	{
		return MemberSocialMedia::accounts($this->id)->get();
	}

	public function getUpdatesCountAttribute()
	{
		return CircleMessageMember::where("member_id", "=", $this->id)->where("status", "!=", "read")->count();
	}

	// Normalize the name; by removing Harakat's, and Madd's.
	public static function normalize($name)
	{
		// Remove every non Arabic letters (chars).
		$name = preg_replace("/[^أاإآبتثجحخدذرزسشصضطظعغفقكلمنهوؤيئءىﻻﻵة ]/u", "", $name);

		// Normalize [Abd].
		$name = preg_replace("/عبد /", "عبد", $name);

		// Null the name if empty.
		$name = empty($name) ? null : $name;

		return $name;
	}

	// Tells the caller that $trustee_id is able to use the called resource for $member_id or not.
	public static function canUseResource($trustee_id, $member_id)
	{
		// Check if the trustee is the member.
		if ($trustee_id == $member_id)
		{
			return true;
		}

		// Has the trustee been a trustee before?
		$trustees_count = Trustee::where("trustee_id", "=", $trustee_id)->count();

		if ($trustees_count == 0)
		{
			// Check the relations of the member.
			$found_relation = MemberRelation::where("member_a", "=", $member_id)->where("member_b", "=", $trustee_id)->where("active", "=", 1)->count();

			if ($found_relation == 0)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			// Check this specific trust.
			$found_trust = Trustee::where("member_id", "=", $member_id)->where("trustee_id", "=", $trustee_id)->where("active", "=", 1)->count();

			if ($trustees_count == 0)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
	}

	public static function getFullname($id, $visited_nodes = array())
	{
		$member = self::find($id);

		if (is_null($member))
		{
			return null;
		}

		if ($member->is_root == 1)
		{
			return $member->fullname;
		}

		// Get the father of the current member.
		$father_relation = $member->inRelations()->where("relationship", "=", "father")->first();

		if (!is_null($father_relation))
		{
			return $member->name . " " . self::getFullname($father_relation->member_a);
		}

		// Done.
		return $member->name;
	}

	// Get the tribe id or root id.
	public static function getTribeId($id)
	{
		$member = self::find($id);

		while ($member != null)
		{
			if (!is_null($member->tribe_id))
			{
				return $member->tribe_id;
			}

			// Get the father of the current member.
			$father_relation = $member->inRelations()->where("relationship", "=", "father")->first();

			if (!is_null($father_relation))
			{
				$member = Member::find($father_relation->member_a);
			}
			else
			{
				return $member->id;
			}
		}
	}

	// The concept might be different.
	// $member_id is $tribe_id at the begining.
	public static function updateTribeFullnames($member_id)
	{
		$member = self::find($member_id);

		if (is_null($member))
		{
			return;
		}

		// Get the fullname of the current member.
		$fullname = self::getFullname($member->id);

		// Update it.
		$member->update(array(
			"fullname" => $fullname
		));

		// Update the children for the current member.
		if ($member->gender == "male")
		{
			// Get the children of the member (out).
			$children = $member->outRelations()->where("relationship", "=", "father")->get();

			foreach ($children as $child)
			{
				self::updateTribeFullnames($child->member_b);
			}
		}
	}

	// Update the tribe ids for the member and the children.
	public static function updateTribeIds($member_id, $tribe_id)
	{
		$member = self::find($member_id);

		if (is_null($member))
		{
			return;
		}

		// Update it.
		$member->update(array(
			"tribe_id" => $tribe_id
		));

		// Update the children for the current member.
		if ($member->gender == "male")
		{
			// Get the children of the member (out).
			$children = $member->outRelations()->where("relationship", "=", "father")->get();

			foreach ($children as $child)
			{
				self::updateTribeIds($child->member_b, $tribe_id);
			}
		}
	}

	public static function dashboard($id)
	{
		// Get the member details.
		// TODO: Get the updates count.
		return self::with(array("inRelations" => function($query){
			$query->with("firstMember");
		}))->find($id);
	}

	public function getViewsCountAttribute()
	{
		return Action::calculate("view", "member", $this->id);
	}

	public function getLikesCountAttribute()
	{
		return Action::calculate("like", "member", $this->id);
	}

	public function getCommentsCountAttribute()
	{
		return Action::calculate("comment", "member", $this->id);
	}

	public function getMediasCountAttribute()
	{
		return Media::where("created_by", "=", $this->id)->count();
	}
}
