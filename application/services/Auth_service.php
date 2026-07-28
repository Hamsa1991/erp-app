<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'services/Base_service.php';

class Auth_service extends Base_service {

	public function __construct()
	{
		parent::__construct();
		$this->CI->load->model('user_model');
		$this->CI->load->model('permission_model');
		$this->CI->load->library('session');
	}

	public function login($email, $password)
	{
		$user = $this->CI->user_model->get_by_email($email);

		if ( ! $user || ! $user->is_active || ! password_verify($password, $user->password))
		{
			return $this->error('Invalid email or password');
		}

		$user_with_roles = $this->CI->user_model->get_with_roles($user->id);
		unset($user_with_roles->password);

		$permissions = $this->CI->permission_model->get_for_user($user->id);
		$user_with_roles->permissions = array_map(function ($row) {
			return $row->slug;
		}, $permissions);

		$this->CI->session->set_userdata('auth_user', $user_with_roles);

		return $this->success($user_with_roles, 'Login successful');
	}

	public function logout()
	{
		$this->CI->session->unset_userdata('auth_user');
		return $this->success(NULL, 'Logged out');
	}

	public function current_user()
	{
		return $this->CI->session->userdata('auth_user');
	}

	public function current_user_has_permission($slug)
	{
		$user = $this->current_user();

		if ( ! $user || empty($user->permissions))
		{
			return FALSE;
		}

		return in_array($slug, $user->permissions, TRUE);
	}

	public function current_user_has_role($role_name)
	{
		$user = $this->current_user();

		if ( ! $user || empty($user->roles))
		{
			return FALSE;
		}

		foreach ($user->roles as $role)
		{
			if ($role->name === $role_name)
			{
				return TRUE;
			}
		}

		return FALSE;
	}
}
