<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Controller.php';

class Roles extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->service('role_service');
		$this->load->service('auth_service');

		if ( ! $this->auth_service->current_user())
		{
			$this->json_error('Unauthorized', 401);
			exit;
		}
	}

	public function index()
	{
		$this->require_permission('manage_roles');
		$result = $this->role_service->list_all();
		$this->json_success($result['data']);
	}

	public function permissions()
	{
		$this->require_permission('manage_roles');
		$result = $this->role_service->list_permissions();
		$this->json_success($result['data']);
	}
}
