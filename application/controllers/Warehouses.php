<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/Web_Controller.php';

class Warehouses extends Web_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->service('warehouse_service');
		$this->load->service('auth_service');
	}

	private function require_api_auth()
	{
		if ( ! $this->auth_service->current_user())
		{
			$this->json_error('Unauthorized', 401);
			exit;
		}
	}

	public function browse()
	{
		$this->require_auth();
		$this->require_permission_web('view_warehouses');

		$this->render('warehouses/browse', array(
			'title' => 'Warehouses',
			'page_script' => 'warehouses.js',
		));
	}

	public function browse_data()
	{
		$this->require_api_auth();
		$this->require_permission('view_warehouses');

		$user = $this->auth_service->current_user();

		// Check if user is a warehouse user
		if ($this->is_warehouse_user($user)) {
			// If they have a warehouse_id, only show that warehouse
			if (!empty($user->warehouse_id)) {
				$warehouse = $this->warehouse_service->get($user->warehouse_id);
				if ($warehouse['success']) {
					// Format as paginated response
					$result = array(
						'success' => true,
						'data' => array(
							'items' => array($warehouse['data']),
							'total' => 1,
							'page' => 1,
							'per_page' => 10,
							'total_pages' => 1
						)
					);
					$this->json_success($result['data']);
					return;
				}
			}

			// No warehouse assigned
			$result = array(
				'success' => true,
				'data' => array(
					'items' => array(),
					'total' => 0,
					'page' => 1,
					'per_page' => 10,
					'total_pages' => 0
				)
			);
			$this->json_success($result['data']);
			return;
		}

		// For admin or other users with view permission
		$params = array(
			'page' => $this->input->get('page'),
			'per_page' => $this->input->get('per_page') ?: 10,
			'search' => $this->input->get('search'),
		);

		$result = $this->warehouse_service->list_paginated($params);
		$this->json_success($result['data']);
	}

	public function manage()
	{
		$this->require_auth();
		$this->require_permission_web('manage_warehouses');

		$user = $this->auth_service->current_user();

		// If warehouse user, redirect to browse or show limited view
		if ($this->is_warehouse_user($user)) {
			// Option 1: Redirect to browse
			redirect('warehouses/browse');
			return;

			// Option 2: Or show a message
			// $this->render('warehouses/manage', array(
			// 	'title' => 'Manage Warehouses',
			// 	'page_script' => 'warehouses-manage.js',
			// 	'limited' => true,
			// 	'warehouse_id' => $user->warehouse_id
			// ));
		}

		$data = array(
			'title' => 'Manage Warehouses',
			'page_script' => 'warehouses-manage.js',
		);

		$this->render('warehouses/manage', $data);
	}

	public function index()
	{
		$this->require_api_auth();
		$this->require_permission('view_warehouses');

		$user = $this->auth_service->current_user();

		// For warehouse users, only return their warehouse
		if ($this->is_warehouse_user($user)) {
			if (!empty($user->warehouse_id)) {
				$result = $this->warehouse_service->get($user->warehouse_id);
				if ($result['success']) {
					$this->json_success(array($result['data']));
					return;
				}
			}
			$this->json_success(array());
			return;
		}

		$result = $this->warehouse_service->list_all();
		$this->json_success($result['data']);
	}

	public function accessible()
	{
		$this->require_api_auth();
		$user = $this->auth_service->current_user();
		$result = $this->warehouse_service->list_accessible($user);
		$this->json_success($result['data']);
	}

	public function show($id)
	{
		$this->require_api_auth();
		$this->require_permission('view_warehouses');

		$user = $this->auth_service->current_user();

		// Check if warehouse user can access this specific warehouse
		if ($this->is_warehouse_user($user)) {
			if (empty($user->warehouse_id) || (int)$user->warehouse_id !== (int)$id) {
				$this->json_error('You do not have permission to view this warehouse', 403);
				return;
			}
		}

		$result = $this->warehouse_service->get($id);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 404);
			return;
		}

		$this->json_success($result['data']);
	}

	public function create()
	{
		$this->require_api_auth();
		$this->require_permission('manage_warehouses');

		// Warehouse users cannot create warehouses
		$user = $this->auth_service->current_user();
		if ($this->is_warehouse_user($user)) {
			$this->json_error('You do not have permission to create warehouses', 403);
			return;
		}

		if ($this->input->method() !== 'post')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->warehouse_service->create($this->get_json_input());

		// Use the parent's handle_service_result method
		$this->handle_service_result($result, 201);
	}

	public function update($id)
	{
		$this->require_api_auth();
		$this->require_permission('manage_warehouses');

		if ( ! in_array($this->input->method(), array('put', 'patch', 'post'), TRUE))
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$user = $this->auth_service->current_user();

		// Warehouse users can only update their own warehouse
		if ($this->is_warehouse_user($user)) {
			if (empty($user->warehouse_id) || (int)$user->warehouse_id !== (int)$id) {
				$this->json_error('You do not have permission to update this warehouse', 403);
				return;
			}
		}

		$result = $this->warehouse_service->update($id, $this->get_json_input());

		// Use the parent's handle_service_result method
		$this->handle_service_result($result);
	}

	public function delete($id)
	{
		$this->require_api_auth();
		$this->require_permission('manage_warehouses');

		if ($this->input->method() !== 'delete')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		// Warehouse users cannot delete warehouses
		$user = $this->auth_service->current_user();
		if ($this->is_warehouse_user($user)) {
			$this->json_error('You do not have permission to delete warehouses', 403);
			return;
		}

		$result = $this->warehouse_service->delete($id);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 404);
			return;
		}

		$this->json_success(NULL, $result['message']);
	}

	// Helper method to check if user is a warehouse user
	private function is_warehouse_user($user)
	{
		if (empty($user->roles))
		{
			return FALSE;
		}

		foreach ($user->roles as $role)
		{
			if ($role->name === 'user_warehouse' || $role->name === 'warehouse_user')
			{
				return TRUE;
			}
		}

		return FALSE;
	}
}
