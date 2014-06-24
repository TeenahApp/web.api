<?php

class MembersController extends \Controller {

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
	public function store()
	{
		//
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
		$user = User::current();

		if (!Member::canUseResource($user->member_id, $id))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		// Get the member if exists.
		//$member = Member::with("jobs")->with("educations")->find($id);
		// Fixed an issue with displaying inner relations to this member.

		$member = Member::with("jobs")->with("educations")->with(array("inRelations" => function($query){
			$query->with("firstMember");
		}))->find($id);

		if (is_null($member))
		{
			return Response::json(array(
				"message" => "Cannot find the resource."
			), 404);
		}

		// TODO: Check if the current member liked the comment.
		$member_likes_count = Action::where("area", "=", "member")->where("action", "=", "like")->where("affected_id", "=", $member->id)->where("created_by", "=", $user->member_id)->count();
		$member->has_liked = ($member_likes_count > 0) ? 1 : 0;

		// Make an action for the logged in user (member); specifically "view".
		Action::view("member", $member->id);

		// Done.
		return $member;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// Get the current logged in user.
		$user = User::current();

		if (!Member::canUseResource($user->member_id, $id))
		{
			return Response::json(array(
				"message" => "Not authorized to use this resource."
			), 403);
		}

		$validator = Validator::make(
			array(
				"dob" => Input::get("dob"),
				"dod" => Input::get("dod"),
				"email" => Input::get("email"),
				"marital_status" => Input::get("marital_status")
			),
			array(
				"dob" => "date",
				"dod" => "date",
				"email" => "email",
				"marital_status" => "required|in:single,married,divorced,widow"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Get the chosen member.
		$member = Member::find($id);

		// Make dob and dod null.
		$dob = Input::get("dob") ? : null;
		$dod = Input::get("dod") ? : null;

		if (is_null($member))
		{
			return Response::json(array(
				"message" => "Cannot find the resource."
			), 404);
		}

		// Update the member.
		$member->update(array(
			"dob" => $dob,
			"pob" => Input::get("pob"),
			"dod" => $dod,
			"pod" => Input::get("pod"),
			"email" => Input::get("email"),
			"marital_status" => Input::get("marital_status")
		));

		// Done.
		return Response::json("", 204);
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

// iVBORw0KGgoAAAANSUhEUgAAAIgAAACKCAYAAABxcgJZAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A
// /wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB90LGhEaIlKpUcMAAAAZdEVYdENv
// bW1lbnQAQ3JlYXRlZCB3aXRoIEdJTVBXgQ4XAAAPIklEQVR42u2df0wUZxrHv8xusC4stopyi2xJ
// jSk/RFsUUxCuaf/ApdofqTRc1JjYasP96Flte+mlzVVtk+aa0/PqNU1NtDUhninRGtuexx53qb2K
// 2CiKCOzSePZ0gS0IerIs1D1Y7491xnd+LQs7MzvI8/3LwWF3mPczz/O8z/s87yTt+MJ3CySSiji6
// BSQChESAkAgQEgFCIkBIBAiJACERICQSAUIiQEgECIkAIREgJAKENNlkpVugrQa7zuP7/lsYunEV
// 3T3XMBoOy86xcBwyM2bCNmM2HpiVhNS5DxEgd7MutDRHBUKq0XAYPn8f4O9DBwCgHXa7HZlzs7Bw
// 0cOm+tuSqKIsPmvR0OJDIBDQ7DMtHIf5D+aYBhQCRCcw+IF+YFYSsn8yU/j55R+u4fv+W+i61IbB
// EGd6UAiQGGMKXt1dnapgOB3pQlzBQqGmyz9cAwC0XeqNGq/MfzBHODY6ZiFAoujbb+ojsYLJZLfb
// 4XriSQpSE205eDhSk8Pmua4Qh0AggG+/qccjPy0nQBKltku9gokvzQqa5rp6ghY099wDn78Pj5AF
// SbxGw2E0dE43zfUMj1gBhGG328nFJEpHDn0qChijzTaMV+S6jHIzBIhCYDoaDsPCcZhuHTFvjBTi
// 4PP3YUHXeV1nNQSIRHxgOt06gtKsYdNep/tSihArPTKXLIihU8hAIIDBEGeq2ENN3T3XyMUYpQst
// zaIkmLliD/Ug+kJLs24ZVwKE0cXvOkTHFaX5UTOix9v60eH1RP3M1OQwkqbNEI6HgsGoC3qpyWGU
// LS0YMxP7l7+fF2Du8HoIECOsBztw1ZVlWP9MieK5rb4g3v6gVjHL6nSkY8WyXDiyslGSNwdpNqvi
// 73f13sCpM+dkazqDIQ51De2oKM3HlnUuxd8HAEdWNra9f0AUXOsxo6FU+20dqj0oikMO/WGj4uC4
// m7rxzp8PimCycBzKS3JRueIxFDhTxvW9A0MjaPT0Yl+tWwac3W7HH3+7TvUzq17/RPQ7FaX5ms9o
// qKLs9tPHas3yh1Th2Pb+AREcTkc6Pnrn59havXLccABAms0K15JM1L73PLa9vFaU1g8EAnjl9zVo
// 9SlncjdUuUTHDS0+ze/NlAeEXXPhB1zJtfBwsKquLEPte89PCAwluZZk4vDuV5GTmxcTJK4lmbJz
// B7vOEyBail3KV3oq1eDY9vJa1RglHqXZrNj/xpOoKM2XQTIwJE/cvbbucdExv4ZEgGiVR+jqFPl8
// 15JMWYyw8+PPZXBIz9NaW6tXyiDZVeOWnVfgTBFZEa3LE6Y0IINd50UziDXL5QHerhq36JzqyjLd
// 4WAhYQe/rqEd7qZu2XmPLZglm5ERIDq4l6LFi2Supa6hXTjOyc3Txa1E0+7NFaKV250ffy5zNavK
// l8LC3RnKoRtXCRAtxCbGnI50WbB50H1WlMDavbnC8GtMs1nx6gtPi1zNZ/WnZeeUl+Tq4mamLCCD
// XedF09UVy3JF/9/qC4qypGufelQ1aaW3pLOVYye9snOKiwp1cTNWsw4e33z038GbwpMDRJJStpTI
// k545NwvAxAp5x3Ivh48dF/5t4TisKl+a0Huy2rUY224D6/P3wd3ULYqFSvLmwMJxAvRauRnTAHKh
// pTlqxTiv0XBYtAYBYELNR+wNtHCczL2cON0qGNjyktyEWQ/Wiuy8vdIMAKfOnBMBkmazIjNjpuBe
// +AdrUgMy2HUebZd6NfOZgUAAHV4POrweOB3pWDBvjqplYW9gZsZMmXthV3IrVzxmiodozfKHsOfw
// CdV8x4J5c4R7ORQMTl5AYrUWfHDIa3jEKqryirYc7/P3wefvg93uQ+kipwwU9rsXzJsj+r8zZ1ui
// WpdEyZGVDeCE8PcNDI2ILBu7AhxLC6gpAXH/7UtVMPgyv/n3hQAAGSmjY35eT9ASmZFcT8bwiFV2
// YwKBAOoa2uF09KqudkqX1vmGJiXrkkiV5IlBbvT0itwMCxBvoeNdvLMaaTUuftehSHZqchjz7wvF
// BIRU/O9kpAwLwFy8niyzLj5/H7oPfSqaDopv7B2x5ltqXRKpNJtVqHgDAH/nZYABZO6cGbJAfOHc
// SWBB1KxGPGBEAyYjZVgRlNFwGHUN7bKWAemNjWZdEq17U6epWuACZ4rmMxlrIuDQA4xooFy4ahNZ
// Lun13D9rmszaKLkbM8g2YzZw+/qUrs2WkqLpbgNWo+F4OONHXcFQBiWAhs7pE6oxNZsF0TKNHos4
// o+CwcJzhcLAqzRrGwxk/qvp2Vk5HOiaDlOC9N3Wa4lTeVIAowbFw9lDC4GCtiRok0nwCr+Nt/aYB
// YmBoRNTmIA2uJ4UF+fabelPCwUIi7daXro6yTyZbL5JoXem/KYqllIJr1mqw1sQUgFxoaRYFeGaD
// gxefZ4mekLoT0CpVciVCiUjgaQqItK/EjHDEImlCSrq8niix7o7ddUgUxAaD5gSEb3pmp7KTBY5G
// T68saB1red1oScsPpFVkvKtkx8A2Y7Y5AJFWhqcmh03d+CyVv/Oy7GerXYtFeRGlUj8jFUv5gRT0
// B2YlmQMQaT/GWD7eDIEqK6WEk2tJpijjylaXJWL2Eik/iEit/EAJ9Hhl1cJ6sLOWaK6FX1gzAxip
// yWEhcSYtHuLFLq93eD3Yf3SW4TWpALDpT3UxlR9IQdeiyy5uQKR1CUrWQyndnQipJeq6LrUBkO8a
// uKp8KY6d9Aruc++RkyhavMjQ5f/9RxtFsUdFab7q97NjodUWVXG7GGnsoTQAZoADAJp77hGsGAvy
// YIhTnMqm2ax466Uq4Xg0HMbbH9QaNu11N3Vj75GTwrHTkY4t61yq57NJNC1yIHEDIi2MVYs9zADH
// WFKbyhY4U0QNTD5/HzZur9EdEqUm8Q1V6t3+7qZuzWcwcbsY6cJRLNPa6soyQwuAr/TfxItvfjhm
// oHq8rR/rn1H+jC3rXPi+/5Zg6nlI3nqpShd3owTHWA1bp86cE+egNNovJC5AWJMW62azjqxsQwuA
// C6J8Fxuodng9aPU9rjjgaTYrdm+uwKY/QQTJlu0fYO1Tj2oWuA4MjWBXjVvUrMXDEe077sxyOE3j
// j7hdDEu42ae2SpJeM5vKVoOETaANhjjsOXwC69/9UnWLhvFYjed+s3fccPD5D3aWw7eDJNSCaL3N
// QKKmvWwF1rGT3qiDwUOyqyZJNJAdXg9efDNSSb+hyqW6s5BUrb4gzpxtEW0nxVq3136xLqY+YL3c
// S1yASHMHk3HNBYhsd8k/fT5/H1p9wahxRZrNiq3VK1FcVCjbFcjn7xO2ibDb7bg3dZoQLA7duCr6
// 9/X+XtUCppzcPOzeXBETZANDI6hv9Go+vY0bEGnj0WTV/PtCaO65RzjeUfMV9r8x9psUXEsyUZK3
// Dp/Vn8aBL/4lG+xAIBCxCmzPj6j/R37PeAs0nt0DdtW4Ra5eS/eiSR6Efwons5thA+wOryemdRc+
// oDx20qvZdpndPddw6sy5mOOZVl9Q5Orsdrvmux1O2IJo1dpnRiuyr9YN15LnVcH4rP409h45qXl+
// h6+6j/TxjB3P7Kj5SnSstfWIe5o7Ee2rdcPfmWtKK8LGIvuPNsoC1v1HG8cEQ+mtU2wBEr+gxr+a
// TK3DkI9nnI50xXyLu6lblILXw3oAcWyDydadjrW8z+8rbgZFK5xmr5PdCrPVF8SOmq9km+ZWlOYL
// +5zm5OaJSgTGK6U9U6XT3VXlS5Fms2JgaAQbt9fovgUmAFiWr3ll20R+8d8Xv0MoFMkjJFtu4f40
// 9TikJ2hFaDQp4XCkJoeRlx6K6TpDoRC+brqI4NCPePvDI7h69aoMjq3VKzE68j80ea7g5kAvli2d
// +ABlZTqwrDAHOVkzMHgrBf194ob2Js8VfN10EXm5D+Ljw//E2fbLIqs1r2CZLvfMEAsCwBQb48dS
// xPSP/9jHjC14OPiYpHLTTgyGOFSU5ss2cpmo/J2XRSvJqk84x+HZ536m2z2zmmlwzKCFs4dEAWs0
// OPi8SNnSAtQ1tKOhxacZII6sbGyoysapM+dQ3+hVhVap19h009y7SdESflI4ePEFPIFAQPOqruKi
// Qmx8dpliM5fTka77K1IJkBhdoRocgLgkQI8C54g1cckg8fn7dF/yIEBEQapFMekVDQ6pFfH5+3Sp
// DQWgCEldQ7uukBAgDBxKsUcscPBWhF/p1bNdUwkSPTbxJ0Akung9ecJw8OLzIB1ej25WRAmSQCCg
// 6e7KBEgMrmW8cACRBTx+4PRu+l6xLFe0SNrh9ejiajiCQ+5aJgIHO3CAvA1Vj8BVOsWl98UY4Fri
// gQOItErY7ZFkm7SQR2sVFxXKXA29L0Zjsa4lXjiASOKMf2uEnsGj1GLpZUWmNCBszkMLOFgrYuE4
// BAIB3a2IIytb17dOcWQ9IorWkDQRK8LHB0ZYEWmnv5ZvnZqygEj7hFf88n1s3/PXuKvTeemZfley
// ImwtKr0ORIfglK/mevHND7H+3S/j3u5B7/S7VKWLnGLrqJGbmbKADI/cWch2OtJRUZovzAg6vB5s
// e/8Aql7/JC5QjEi/szMaVmo7FhAgMUrax1pcVIgNVS5UV5YJoPBlf8t/9RH2H20cdz+uUel3Xqyb
// 0WrzvSkJiDT+YHfi4VdOqyvLkJObJ8xG9hw+gcpNO8cNilHpd0BctKzVXmWUaofyhrSOrGysdi3G
// 7369GhWl+bBwnNBqOZ6A1sj0Owv6aDisSRwyJQFhA1QLx425IW1xUaEACp8lHU9Aa1T6XQq6FnHI
// lLcg/PvvYg0EX33haVGcEktAa2T6XWtNeUAmshMPG6fEEtAalX7XY2tuikHiHJBYA1oj0+8EiAlB
// YQNavkuPDWiv9N80NP2ulaw0vNqquKgQxUWFok45vt+Wz1Pw6Xcj3tZAFsTEoEgDWratkt29kCzI
// lI9TsmWdcuOZPREgUwyUyz9c06wDjwC5C0GZDLEHxSAkAoREgJAIEBIBQiJASAQIiQAhESAkEgFC
// IkBIBAiJACERICQChESAkAgQEgFCIhEgpHFIk5rUwRBnivfBjOd6efn8fdhX674rBlOP9wha9bjp
// k01a7ulFFuS2XE9E3i17qPYg3UUT6rmq1eaxIFq/7dkoqb1AcLL/XVrtLgTE8c46Es1iSCQChESA
// kAgQEgFCIkBIBAiJACERICQChEQiQEgECCk+/R89dN/Gew+CCwAAAABJRU5ErkJggg==

	// Base64 photo to be uploaded.
	public function uploadPhoto($member_id)
	{
		// Check if the member does exist and is a female.
		$member = Member::where("id", "=", $member_id)->where("gender", "=", "male")->first();

		if (is_null($member))
		{
			return Response::json(array(
				"message" => "Not authorized to access this resource."
			), 403);
		}

		// Check if the (logged in) member is able to upload a photo for the chosen member.
		$logged_in_user = User::current();

		if (!Member::canUseResource($logged_in_user->member_id, $member->id))
		{
			return Response::json(array(
				"message" => "Not authorized to access this resource."
			), 403);
		}
		
		$validator = Validator::make(
			array(
				"data" => Input::get("data"),
				"extension" => Input::get("extension"),
			),
			array(
				"data" => "required",
				"extension" => "required"
			)
		);

		if ($validator->fails())
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		$media = Media::upload("image", Input::get("data"), Input::get("extension"));
		
		if (is_null($media))
		{
			return Response::json(array(
				"message" => "Bad request."
			), 400);
		}

		// Update the photo for the member that is chosen.
		$member->photo = $media->url;
		$member->save();

		// Done.
		return Response::json(array(
			"url" => $media->url
		), 200);
	}

	public function getComments($member_id)
	{
		$user = User::current();

		// TODO: Check if the user is able to access this resource.

		$comments = Action::get("comment", "member", $member_id);

		// Get the likes of the event.
		$comments->each(function($comment) use ($user)
		{
			$likes_count = Action::calculate("like", "member_comment", $comment->id);

			// Check if the current member liked the comment.
			$member_likes_count = Action::where("area", "=", "member_comment")->where("action", "=", "like")->where("affected_id", "=", $comment->id)->where("created_by", "=", $user->member_id)->count();

			$comment->likes_count = $likes_count;
			$comment->has_liked = ($member_likes_count > 0) ? 1 : 0;
		});

		return $comments;
	}

	public function getMemberByMobile($mobile)
	{
		$member = Member::where("mobile", "=", $mobile)->select(array("id", "name"))->first();

		if ($member == null)
		{
			return Response::json(array(
				"message" => "Member has not been found."
			), 404);
		}

		return $member;
	}

}
