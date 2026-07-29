<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/Web_Controller.php';

class Warehouses extends Web_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->service('warehouse_service');
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

		$params = array(
			'page' => $this->input->get('page'),
			'per_page' => $this->input->get('per_page') ?: 10,
			'search' => $this->input->get('search'),
		);

		$result = $this->warehouse_service->list_paginated($params);
		$this->json_success($result['data']);
	}

	public function index()
	{
		$this->require_api_auth();
		$this->require_permission('view_warehouses');
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

		if ($this->input->method() !== 'post')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->warehouse_service->create($this->get_json_input());
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

		$result = $this->warehouse_service->update($id, $this->get_json_input());
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

		$result = $this->warehouse_service->delete($id);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 404);
			return;
		}

		$this->json_success(NULL, $result['message']);
	}
}
