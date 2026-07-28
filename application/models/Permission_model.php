<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Model.php';

class Permission_model extends MY_Model {

	protected $table = 'permissions';

	public function get_by_slug($slug)
	{
		return $this->get_one_where(array('slug' => $slug));
	}

	public function get_for_user($user_id)
	{
		return $this->db
			->select('permissions.slug')
			->distinct()
			->from('permissions')
			->join('role_permissions', 'role_permissions.permission_id = permissions.id')
			->join('user_roles', 'user_roles.role_id = role_permissions.role_id')
			->where('user_roles.user_id', $user_id)
			->get()
			->result();
	}
}
