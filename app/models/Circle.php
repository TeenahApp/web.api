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
		$age_cases = array(
			"WHEN age >= 0 AND age <= 5 THEN '0-5'",
			"WHEN age >= 6 AND age <= 10 THEN '6-10'",
			"WHEN age >= 11 AND age <= 15 THEN '11-15'",
			"WHEN age >= 16 AND age <= 20 THEN '16-20'",
			"WHEN age >= 21 AND age <= 25 THEN '21-25'",
			"WHEN age >= 26 AND age <= 30 THEN '26-30'",
			"WHEN age >= 31 AND age <= 35 THEN '31-35'",
			"WHEN age >= 36 AND age <= 40 THEN '36-40'",
			"WHEN age >= 41 AND age <= 45 THEN '41-45'",
			"WHEN age >= 46 AND age <= 50 THEN '46-50'",
			"WHEN age >= 51 AND age <= 55 THEN '51-55'",
			"WHEN age >= 56 AND age <= 60 THEN '56-60'",
			"WHEN age >= 61 AND age <= 65 THEN '61-65'",
			"WHEN age >= 66 AND age <= 70 THEN '66-70'",
			"WHEN age >= 71 AND age <= 75 THEN '71-75'",
			"WHEN age >= 76 AND age <= 80 THEN '76-80'",
			"WHEN age >= 81 AND age <= 85 THEN '81-85'",
			"WHEN age >= 86 AND age <= 90 THEN '86-90'",
			"WHEN age >= 91 AND age <= 95 THEN '91-95'",
			"WHEN age >= 96 AND age <= 100 THEN '96-100'",
			"WHEN age >= 101 AND age <= 105 THEN '101-105'",
			"WHEN age >= 106 AND age <= 110 THEN '106-110'",
			"WHEN age >= 111 AND age <= 115 THEN '111-115'",
			"WHEN age >= 116 AND age <= 120 THEN '116-120'",
			"ELSE '>120'"
		);

		// Make the age cases query.
		$age_cases_query = implode(" ", $age_cases);

		// Set the ages.
		$stats["ages"] = Member::whereIn("id", $circle_members)->select(
			array(
				DB::raw("COUNT(id) AS counts"),
				DB::raw("(CASE $age_cases_query END) AS ranges")
			)
		)->groupBy("ranges")->orderBy("age", "ASC")->get()->toArray();

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
