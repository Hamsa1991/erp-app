<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'services/Base_service.php';

class User_service extends Base_service {

	public function __construct()
	{
		parent::__construct();
		$this->CI->load->model('user_model');
		$this->CI->load->model('role_model');
		$this->CI->load->model('warehouse_model');
	}

	public function list_all()
	{
		$users = $this->CI->user_model->get_all(array(), 'last_name ASC');

		foreach ($users as &$user)
		{
			unset($user->password);
			$user->roles = $this->CI->user_model->get_with_roles($user->id)->roles;
		}

		return $this->success($users);
	}

	public function get($id)
	{
		$user = $this->CI->user_model->get_with_roles($id);

		if ( ! $user)
		{
			return $this->error('User not found');
		}

		unset($user->password);
		return $this->success($user);
	}

	public function create(array $data)
	{
		$validation = $this->validate($data);

		if ( ! $validation['success'])
		{
			return $validation;
		}

		if ($this->CI->user_model->get_by_email($data['email']))
		{
			return $this->error('Email already exists');
		}

		$id = $this->CI->user_model->insert(array(
			'first_name' => $data['first_name'],
			'last_name' => $data['last_name'],
			'email' => $data['email'],
			'password' => password_hash($data['password'], PASSWORD_BCRYPT),
			'warehouse_id' => isset($data['warehouse_id']) ? (int) $data['warehouse_id'] : NULL,
			'is_active' => isset($data['is_active']) ? (int) $data['is_active'] : 1,
		));

		if ( ! empty($data['role_ids']) && is_array($data['role_ids']))
		{
			$this->CI->user_model->sync_roles($id, $data['role_ids']);
		}

		$created = $this->get($id);
		return $this->success($created['data'], 'User created');
	}

	public function update($id, array $data)
	{
		$user = $this->CI->user_model->get_by_id($id);

		if ( ! $user)
		{
			return $this->error('User not found');
		}

		if (isset($data['email']) && $data['email'] !== $user->email)
		{
			$existing = $this->CI->user_model->get_by_email($data['email']);

			if ($existing && (int) $existing->id !== (int) $id)
			{
				return $this->error('Email already exists');
			}
		}

		$payload = array();

		foreach (array('first_name', 'last_name', 'email', 'warehouse_id', 'is_active') as $field)
		{
			if (array_key_exists($field, $data))
			{
				$payload[$field] = in_array($field, array('warehouse_id', 'is_active'), TRUE)
					? (int) $data[$field]
					: $data[$field];
			}
		}

		if ( ! empty($data['password']))
		{
			$payload['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
		}

		if ( ! empty($payload))
		{
			$this->CI->user_model->update($id, $payload);
		}

		if (isset($data['role_ids']) && is_array($data['role_ids']))
		{
			$this->CI->user_model->sync_roles($id, $data['role_ids']);
		}

		$updated = $this->get($id);
		return $this->success($updated['data'], 'User updated');
	}

	public function delete($id)
	{
		if ( ! $this->CI->user_model->get_by_id($id))
		{
			return $this->error('User not found');
		}

		$this->CI->user_model->delete($id);
		return $this->success(NULL, 'User deleted');
	}

	private function validate(array $data)
	{
		$errors = array();

		if (empty($data['first_name']))
		{
			$errors['first_name'] = 'First name is required';
		}

		if (empty($data['last_name']))
		{
			$errors['last_name'] = 'Last name is required';
		}

		if (empty($data['email']))
		{
			$errors['email'] = 'Email is required';
		}

		if (empty($data['password']))
		{
			$errors['password'] = 'Password is required';
		}

		if ( ! empty($data['warehouse_id']) && ! $this->CI->warehouse_model->get_by_id($data['warehouse_id']))
		{
			$errors['warehouse_id'] = 'Invalid warehouse';
		}

		if ( ! empty($errors))
		{
			return $this->error('Validation failed', $errors);
		}

		return $this->success();
	}
}
