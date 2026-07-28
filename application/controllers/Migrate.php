<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		if ( ! is_cli() && ENVIRONMENT === 'production')
		{
			show_error('Migrations can only be run from CLI in production.');
		}
	}

	public function index()
	{
		$this->load->library('migration');

		if ($this->migration->latest() === FALSE)
		{
			show_error($this->migration->error_string());
		}

		echo "Migrations completed successfully.\n";
	}

	public function version($version = NULL)
	{
		if ($version === NULL)
		{
			show_error('Migration version is required.');
		}

		$this->load->library('migration');

		if ($this->migration->version($version) === FALSE)
		{
			show_error($this->migration->error_string());
		}

		echo "Migrated to version {$version} successfully.\n";
	}
}
