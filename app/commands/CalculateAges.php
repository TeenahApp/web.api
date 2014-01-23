<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CalculateAges extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'calculate:ages';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Calculate ages for all members.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// Update the ages of dead members (may Allah be merciful to them).
		$dead_members = Member::whereNotNull("dob")->whereNotNull("dod")->where("is_alive", "=", "0")->where("age", "=", "0")->get();

		foreach ($dead_members as $dead_member)
		{
			$dob = new DateTime($dead_member->dob);
			$dod = new DateTime($dead_member->dod);
			$age = $dob->diff($dod)->y;

			if ($age > 0)
			{
				$dead_member->update(array(
					"age" => $age
				));
			}
		}

		// Update the ages of alive members.
		$alive_members = Member::whereNotNull("dob")->where("is_alive", "=", "1")->get();

		foreach ($alive_members as $alive_member)
		{
			$now = new DateTime();
			$dob = new DateTime($alive_member->dob);
			$age = $now->diff($dob)->y;

			if ($age > 0)
			{
				$alive_member->update(array(
					"age" => $age
				));
			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
