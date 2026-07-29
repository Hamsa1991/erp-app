<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'services/Base_service.php';

class Client_service extends Base_service {

	public function __construct()
	{
		parent::__construct();
		$this->CI->load->model('client_model');
	}

	public function list_all($search = NULL)
	{
		if ($search)
		{
			return $this->success($this->CI->client_model->search($search));
		}

		return $this->success($this->CI->client_model->get_all(array(), 'last_name ASC'));
	}

	public function list_all_paginated($search = NULL, $page = 1, $per_page = 10)
	{
		$offset = ($page - 1) * $per_page;

		// Get total count
		$total = $this->CI->client_model->count_all($search);

		// Get paginated results
		if ($search) {
			$clients = $this->CI->client_model->search_paginated($search, $per_page, $offset);
		} else {
			$clients = $this->CI->client_model->get_all_paginated(array(), 'last_name ASC', $per_page, $offset);
		}

		$pagination = array(
			'total' => (int) $total,
			'per_page' => (int) $per_page,
			'current_page' => (int) $page,
			'total_pages' => (int) ceil($total / $per_page)
		);

		return $this->success($clients, 'Success', $pagination);
	}

	public function get($id)
	{
		$client = $this->CI->client_model->get_by_id($id);

		if ( ! $client)
		{
			return $this->error('Client not found');
		}

		return $this->success($client);
	}

	public function create(array $data)
	{
		$validation = $this->validate($data);

		if ( ! $validation['success'])
		{
			return $validation;
		}

		$id = $this->CI->client_model->insert(array(
			'first_name' => $data['first_name'],
			'last_name' => $data['last_name'],
			'email' => isset($data['email']) ? $data['email'] : NULL,
			'phone' => isset($data['phone']) ? $data['phone'] : NULL,
		));

		return $this->success($this->CI->client_model->get_by_id($id), 'Client created');
	}

	public function update($id, array $data)
	{
		if ( ! $this->CI->client_model->get_by_id($id))
		{
			return $this->error('Client not found');
		}

		$payload = array();

		foreach (array('first_name', 'last_name', 'email', 'phone') as $field)
		{
			if (array_key_exists($field, $data))
			{
				$payload[$field] = $data[$field];
			}
		}

		$this->CI->client_model->update($id, $payload);

		return $this->success($this->CI->client_model->get_by_id($id), 'Client updated');
	}

	public function delete($id)
	{
		if ( ! $this->CI->client_model->get_by_id($id))
		{
			return $this->error('Client not found');
		}

		$this->CI->client_model->delete($id);
		return $this->success(NULL, 'Client deleted');
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

		if ( ! empty($errors))
		{
			return $this->error('Validation failed', $errors);
		}

		return $this->success();
	}
}
