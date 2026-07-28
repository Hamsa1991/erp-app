<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'services/Base_service.php';

class Role_service extends Base_service {

	public function __construct()
	{
		parent::__construct();
		$this->CI->load->model('role_model');
		$this->CI->load->model('permission_model');
	}

	public function list_all()
	{
		$roles = $this->CI->role_model->get_all(array(), 'name ASC');

		foreach ($roles as &$role)
		{
			$role->permissions = $this->CI->role_model->get_permissions($role->id);
		}

		return $this->success($roles);
	}

	public function list_permissions()
	{
		return $this->success($this->CI->permission_model->get_all(array(), 'name ASC'));
	}
}
