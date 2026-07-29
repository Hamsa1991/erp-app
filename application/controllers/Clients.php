<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Controller.php';

class Clients extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->service('client_service');
		$this->load->service('auth_service');

		if ( ! $this->auth_service->current_user())
		{
			$this->json_error('Unauthorized', 401);
			exit;
		}
	}

	public function index()
	{
		$this->require_permission('view_clients');

		if ($this->input->is_ajax_request()) {
			$search = $this->input->get('search');
			$page = (int) $this->input->get('page') ?: 1;
			$per_page = (int) $this->input->get('per_page') ?: 10;

			$result = $this->client_service->list_all_paginated($search, $page, $per_page);

			if ($result['success']) {
				// Format data for the frontend pagination
				$response_data = array(
					'items' => $result['data'],
					'total' => isset($result['pagination']['total']) ? $result['pagination']['total'] : 0,
					'per_page' => isset($result['pagination']['per_page']) ? $result['pagination']['per_page'] : $per_page,
					'current_page' => isset($result['pagination']['current_page']) ? $result['pagination']['current_page'] : $page,
					'last_page' => isset($result['pagination']['total_pages']) ? $result['pagination']['total_pages'] : 1
				);

				$this->json_success($response_data, 'Success', 200);
			} else {
				$this->json_error($result['message'], 400);
			}
			return;
		}

		// For regular page loads
		$current_user = $this->auth_service->current_user();

		$data['title'] = 'Manage Clients';
		$data['layout'] = 'dashboard';
		$data['page_script'] = 'clients.js';
		$data['current_user'] = $current_user;
		$data['content'] = $this->load->view('clients/browse', NULL, TRUE);

		$this->load->view('layouts/dashboard', $data);
	}

	public function show($id)
	{
		$this->require_permission('view_clients');
		$result = $this->client_service->get($id);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 404);
			return;
		}

		$this->json_success($result['data']);
	}

	public function create()
	{
		$this->require_permission('manage_clients');

		if ($this->input->method() !== 'post')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->client_service->create($this->get_json_input());

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 422, isset($result['errors']) ? $result['errors'] : NULL);
			return;
		}

		$this->json_success($result['data'], $result['message'], 201);
	}

	public function update($id)
	{
		$this->require_permission('manage_clients');

		if ( ! in_array($this->input->method(), array('put', 'patch', 'post'), TRUE))
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->client_service->update($id, $this->get_json_input());

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 422, isset($result['errors']) ? $result['errors'] : NULL);
			return;
		}

		$this->json_success($result['data'], $result['message']);
	}

	public function delete($id)
	{
		$this->require_permission('manage_clients');

		if ($this->input->method() !== 'delete')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->client_service->delete($id);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 404);
			return;
		}

		$this->json_success(NULL, $result['message']);
	}
}
