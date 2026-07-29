<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/Web_Controller.php';

class Products extends Web_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->service('product_service');
		$this->load->service('warehouse_service');
		$this->load->service('product_warehouse_service');
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
		$this->require_permission_web('view_products');

		$this->render('products/browse', array(
			'title' => 'Products',
			'page_script' => 'products.js',
		));
	}

	public function browse_data()
	{
		$this->require_api_auth();
		$this->require_permission('view_products');

		$user = $this->auth_service->current_user();
		$scope = $this->warehouse_service->get_user_warehouse_scope($user);

		$params = array(
			'page' => $this->input->get('page'),
			'per_page' => $this->input->get('per_page') ?: 10,
			'search' => $this->input->get('search'),
			'only_available' => TRUE,
		);

		if ($scope !== NULL && $scope !== FALSE)
		{
			$params['warehouse_id'] = $scope;
		}
		elseif ($this->input->get('warehouse_id'))
		{
			$params['warehouse_id'] = $this->input->get('warehouse_id');
		}

		$result = $this->product_service->list_paginated_with_inventory($params);
		$this->json_success($result['data']);
	}

	public function manage()
	{
		$this->require_auth();
		$this->require_permission_web('manage_products');

		$user = $this->auth_service->current_user();
		$warehouses = $this->warehouse_service->list_accessible($user);

		$this->render('products/manage', array(
			'title' => 'Manage Products',
			'page_script' => 'products-manage.js',
			'warehouses' => $warehouses['data'],
			'is_admin' => $this->auth_service->current_user_has_role('admin'),
		), 'dashboard');
	}

	public function manage_data()
	{
		$this->require_api_auth();
		$this->require_permission('manage_products');

		$params = array(
			'page' => $this->input->get('page'),
			'per_page' => $this->input->get('per_page') ?: 10,
			'search' => $this->input->get('search'),
		);

		$result = $this->product_service->list_paginated_for_manage($params);
		$this->json_success($result['data']);
	}

	public function index()
	{
		$this->require_api_auth();
		$this->require_permission('view_products');
		$result = $this->product_service->list_all();
		$this->json_success($result['data']);
	}

	public function show($id)
	{
		$this->require_api_auth();
		$this->require_permission('view_products');

		$user = $this->auth_service->current_user();
		$scope = $this->warehouse_service->get_user_warehouse_scope($user);
		$result = $this->product_service->get_with_inventory($id, $scope ?: NULL);

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
		$this->require_permission('manage_products');

		if ($this->input->method() !== 'post')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->product_service->create($this->get_json_input());
		$this->handle_service_result($result, 201);
	}

	public function update($id)
	{
		$this->require_api_auth();
		$this->require_permission('manage_products');

		if ( ! in_array($this->input->method(), array('put', 'patch', 'post'), TRUE))
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->product_service->update($id, $this->get_json_input());
		$this->handle_service_result($result);
	}

	public function toggle($id)
	{
		$this->require_api_auth();
		$this->require_permission('manage_products');

		if ($this->input->method() !== 'post')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->product_service->toggle_availability($id);
		$this->handle_service_result($result);
	}

	public function delete($id)
	{
		$this->require_api_auth();
		$this->require_permission('manage_products');

		if ($this->input->method() !== 'delete')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->product_service->delete($id);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 404);
			return;
		}

		$this->json_success(NULL, $result['message']);
	}
}
