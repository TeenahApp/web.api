<?php

class Circle extends Eloquent {

	protected $table = "circles";
	protected $guarded = array();

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}

	public function members()
	{
		// Set the circle right.
		$circle_id = $this->id;

		return DB::table("members")->select("members.*")->join("member_circles", function($join) use ($circle_id){
			$join->on("members.id", "=", "member_circles.member_id")
				->where("circle_id", "=", $circle_id);
		});
	}

	// Get the circle stats.
	public static function stats($circle_members)
	{
		// Members.
		
		// members_count.
		$stats["members_count"] = Member::whereIn("id", $circle_members)->count();

		// males_count.
		$stats["males_count"] = Member::whereIn("id", $circle_members)->where("gender", "=", "male")->count();

		// females_count.
		$stats["females_count"] = Member::whereIn("id", $circle_members)->where("gender", "=", "female")->count();

		// alive_members_count
		$stats["alive_members_count"] = Member::whereIn("id", $circle_members)->where("is_alive", "=", 1)->count();

		// alive_males_count.
		$stats["alive_males_count"] = Member::whereIn("id", $circle_members)->where("is_alive", "=", 1)->where("gender", "=", "male")->count();

		// alive_females_count.
		$stats["alive_females_count"] = Member::whereIn("id", $circle_members)->where("is_alive", "=", 1)->where("gender", "=", "female")->count();

		// TODO: Children average -- second stage.
		// It describes the average number of children for members.

		// TODO: Ages.
		// TODO: Build a cron job (a work) to update ages daily.

		// Educations.
		// educations => nones, etc.
		$stats["educations"] = MemberEducation::whereIn("member_id", $circle_members)->where("status", "=", "ongoing")->groupBy("degree")->select(array("degree", DB::raw("count(*) AS members_count")))->orderBy("members_count", "DESC")->get()->toArray();

		// Majors.
		// education_majors
		$stats["education_majors"] = MemberEducation::whereIn("member_id", $circle_members)->where("status", "=", "ongoing")->groupBy("major_id")->select(array("major_id", DB::raw("count(*) AS members_count")))->orderBy("members_count", "DESC")->with("major")->get()->toArray();

		// Companies.
		// companies.
		$stats["companies"] = MemberJob::whereIn("member_id", $circle_members)->where("status", "=", "ongoing")->groupBy("company_id")->select(array(DB::raw("count(*) AS members_count")))->orderBy("members_count", "DESC")->with("comapny")->get()->toArray();

		// Jobs.
		// jobs.
		$stats["jobs"] = MemberJob::whereIn("member_id", $circle_members)->where("status", "=", "ongoing")->groupBy("title")->select(array("title", DB::raw("count(*) AS members_count")))->orderBy("members_count", "DESC")->get()->toArray();

		// Events.
		// event_count.
		$stats["event_count"] = CircleEventMember::whereIn("member_id", $circle_members)->distinct("event_id")->count();

		// Messages.
		// messages_count.
		$stats["messages_count"] = CircleMessageMember::whereIn("member_id", $circle_members)->distinct("message_id")->count();

		// TODO: Medias.
		// medias_count.
		$stats["medias_count"] = 0;

		// Names.
		// male_names.
		$stats["male_names"] = Member::whereIn("id", $circle_members)->where("gender", "=", "male")->groupBy("name")->select(array("name", DB::raw("count(*) AS members_count")))->orderBy("members_count", "DESC")->get()->toArray();

		// female_names.
		$stats["female_names"] = Member::whereIn("id", $circle_members)->where("gender", "=", "female")->groupBy("name")->select(array("name", DB::raw("count(*) AS members_count")))->orderBy("members_count", "DESC")->get()->toArray();

		// Locations.
		// locations.
		$stats["locations"] = Member::whereIn("id", $circle_members)->groupBy("location")->select(array("location", DB::raw("count(*) AS members_count")))->orderBy("members_count", "DESC")->get()->toArray();

		return $stats;
	}
}
