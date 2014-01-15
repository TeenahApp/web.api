<?php

class Media extends Eloquent {

	protected $table = "medias";
	protected $guarded = array();
	protected $hidden = array("signature");

	public function creator()
	{
		return $this->belongsTo("Member", "created_by");
	}

	// TODO: This uploaded file should be using S3.
	// Returns a Media object or null.
	public static function upload($category, $data, $extension)
	{
		// Get the logged in users.
		$user = User::current();

		$validator = Validator::make(
			array(
				"category" => $category,
				"data" => $data,
				"extension" => $extension
			),
			array(
				"category" => "required|in:image,video,sound",
				"data" => "required",
				"extension" => "required|in:jpg,png,jpeg,gif,mp4,mp3"
			)
		);

		if ($validator->fails())
		{
			return null;
		}

		// TODO: Check if the file is really a valid file; not a maleware.

		// Set the file fullname.
		$output_filename = "uploads/" . str_random(40) . ".{$extension}";
		$output_full_filename = public_path() . "/" . $output_filename;

		// Save the decoded base64 of the file (image).
		$file_contents = base64_decode($data);
		$file_saving = File::put($output_full_filename, $file_contents);

		if ($file_saving == 0)
		{
			return null;
		}

		// Make a unique hash.
		$signature = Hash::make($file_contents);

		// TODO: Try to get a taste of the uploaded file.
		// Meaning: A thumb for the photo.

		// Set the photo with the url.
		// TODO: URL should be under https protocol.
		$media_url = asset($output_filename);

		// Create this media.
		return self::create(array(
			"category" => $category,
			"url" => $media_url,
			"signature" => $signature,
			"created_by" => $user->member_id
		));
	}
}
