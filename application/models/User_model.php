<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Model.php';

class User_model extends MY_Model {

	protected $table = 'users';

	public function get_by_email($email)
	{
		return $this->get_one_where(array('email' => $email));
	}

	public function get_with_roles($id)
	{
		$user = $this->get_by_id($id);

		if ( ! $user)
		{
			return NULL;
		}

		$user->roles = $this->db
			->select('roles.*')
			->from('user_roles')
			->join('roles', 'roles.id = user_roles.role_id')
			->where('user_roles.user_id', $id)
			->get()
			->result();

		return $user;
	}

	public function assign_role($user_id, $role_id)
	{
		$exists = $this->db->get_where('user_roles', array(
			'user_id' => $user_id,
			'role_id' => $role_id,
		))->row();

		if ($exists)
		{
			return TRUE;
		}

		return $this->db->insert('user_roles', array(
			'user_id' => $user_id,
			'role_id' => $role_id,
		));
	}

	public function sync_roles($user_id, array $role_ids)
	{
		$this->db->delete('user_roles', array('user_id' => $user_id));

		if (empty($role_ids))
		{
			return TRUE;
		}

		$rows = array();
		foreach ($role_ids as $role_id)
		{
			$rows[] = array(
				'user_id' => $user_id,
				'role_id' => (int) $role_id,
			);
		}

		return $this->db->insert_batch('user_roles', $rows);
	}
}
