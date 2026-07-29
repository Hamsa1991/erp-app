<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Model.php';

class Role_model extends MY_Model {

	protected $table = 'roles';

	public function get_by_name($name)
	{
		return $this->get_one_where(array('name' => $name));
	}

	public function get_permissions($role_id)
	{
		return $this->db
			->select('permissions.*')
			->from('role_permissions')
			->join('permissions', 'permissions.id = role_permissions.permission_id')
			->where('role_permissions.role_id', $role_id)
			->get()
			->result();
	}

	public function get_users($role_id)
	{
		return $this->db
			->select('users.*')
			->from('user_roles')
			->join('users', 'users.id = user_roles.user_id')
			->where('user_roles.role_id', $role_id)
			->get()
			->result();
	}
}
