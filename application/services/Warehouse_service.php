<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'services/Base_service.php';

class Warehouse_service extends Base_service {

	public function __construct()
	{
		parent::__construct();
		$this->CI->load->model('warehouse_model');
	}

	public function list_all()
	{
		return $this->success($this->CI->warehouse_model->get_all(array(), 'name ASC'));
	}

	public function list_paginated(array $params = array())
	{
		$result = $this->CI->warehouse_model->get_paginated($params);
		return $this->success($result);
	}

	public function list_accessible($user)
	{
		if ($this->is_admin_user($user))
		{
			return $this->list_all();
		}

		if (empty($user->warehouse_id))
		{
			return $this->success(array());
		}

		$warehouse = $this->CI->warehouse_model->get_by_id($user->warehouse_id);

		return $this->success($warehouse ? array($warehouse) : array());
	}

	public function get($id)
	{
		$warehouse = $this->CI->warehouse_model->get_by_id($id);

		if ( ! $warehouse)
		{
			return $this->error('Warehouse not found');
		}

		return $this->success($warehouse);
	}

	public function create(array $data)
	{
		if (empty($data['name']))
		{
			return $this->error('Validation failed', array('name' => 'Name is required'));
		}

		$id = $this->CI->warehouse_model->insert(array(
			'name' => $data['name'],
			'address' => isset($data['address']) ? $data['address'] : NULL,
		));

		return $this->success($this->CI->warehouse_model->get_by_id($id), 'Warehouse created');
	}

	public function update($id, array $data)
	{
		if ( ! $this->CI->warehouse_model->get_by_id($id))
		{
			return $this->error('Warehouse not found');
		}

		$payload = array();

		if (isset($data['name']))
		{
			$payload['name'] = $data['name'];
		}

		if (array_key_exists('address', $data))
		{
			$payload['address'] = $data['address'];
		}

		$this->CI->warehouse_model->update($id, $payload);

		return $this->success($this->CI->warehouse_model->get_by_id($id), 'Warehouse updated');
	}

	public function delete($id)
	{
		if ( ! $this->CI->warehouse_model->get_by_id($id))
		{
			return $this->error('Warehouse not found');
		}

		$this->CI->warehouse_model->delete($id);
		return $this->success(NULL, 'Warehouse deleted');
	}

	public function user_can_access($user, $warehouse_id)
	{
		if ($this->is_admin_user($user))
		{
			return TRUE;
		}

		return ! empty($user->warehouse_id) && (int) $user->warehouse_id === (int) $warehouse_id;
	}

	public function get_user_warehouse_scope($user)
	{
		if ($this->is_admin_user($user))
		{
			return NULL;
		}

		return ! empty($user->warehouse_id) ? (int) $user->warehouse_id : FALSE;
	}

	private function is_admin_user($user)
	{
		if (empty($user->roles))
		{
			return FALSE;
		}

		foreach ($user->roles as $role)
		{
			if ($role->name === 'admin')
			{
				return TRUE;
			}
		}

		return FALSE;
	}
}
