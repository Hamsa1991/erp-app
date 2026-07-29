<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/Web_Controller.php';

class Bills extends Web_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->service('bill_service');
		$this->load->service('warehouse_service');
		$this->load->service('client_service');
	}

	private function require_api_auth()
	{
		if ( ! $this->auth_service->current_user())
		{
			$this->json_error('Unauthorized', 401);
			exit;
		}
	}

	private function get_warehouse_scope()
	{
		$user = $this->auth_service->current_user();
		$scope = $this->warehouse_service->get_user_warehouse_scope($user);

		return ($scope === FALSE) ? -1 : $scope;
	}

	public function browse()
	{
		$this->require_auth();
		$this->require_permission_web('view_bills');

		$this->render('bills/browse', array(
			'title' => 'Bills',
			'page_script' => 'bills.js',
		));
	}

	public function browse_data()
	{
		$this->require_api_auth();
		$this->require_permission('view_bills');

		$scope = $this->get_warehouse_scope();

		if ($scope === -1)
		{
			$this->json_success(array(
				'items' => array(),
				'total' => 0,
				'page' => 1,
				'per_page' => 10,
				'total_pages' => 0,
			));
			return;
		}

		$params = array(
			'page' => $this->input->get('page'),
			'per_page' => $this->input->get('per_page') ?: 10,
		);

		if ($scope !== NULL)
		{
			$params['warehouse_id'] = $scope;
		}

		$result = $this->bill_service->list_paginated($params);
		$this->json_success($result['data']);
	}

	public function detail($id)
	{
		$this->require_auth();
		$this->require_permission_web('view_bills');

		$result = $this->bill_service->get($id);

		if ( ! $result['success'])
		{
			show_404();
			return;
		}

		$user = $this->auth_service->current_user();
		$scope = $this->warehouse_service->get_user_warehouse_scope($user);

		if ($scope !== NULL && (int) $result['data']->warehouse_id !== (int) $scope)
		{
			show_error('Forbidden', 403);
			return;
		}

		$this->render('bills/detail', array(
			'title' => 'Bill #'.$id,
			'bill' => $result['data'],
		));
	}

	public function create_form()
	{
		$this->require_auth();
		$this->require_permission_web('manage_bills');

		$user = $this->auth_service->current_user();
		$warehouses = $this->warehouse_service->list_accessible($user);
		$clients = $this->client_service->list_all();

		$this->render('bills/create', array(
			'title' => 'Create Bill',
			'page_script' => 'bills-create.js',
			'warehouses' => $warehouses['data'],
			'clients' => $clients['data'],
		), 'dashboard');
	}

	public function index()
	{
		$this->require_api_auth();
		$this->require_permission('view_bills');

		$scope = $this->get_warehouse_scope();

		if ($scope === -1)
		{
			$this->json_success(array());
			return;
		}

		$result = $this->bill_service->list_all($scope);
		$this->json_success($result['data']);
	}

	public function show($id)
	{
		$this->require_api_auth();
		$this->require_permission('view_bills');

		$result = $this->bill_service->get($id);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 404);
			return;
		}

		$user = $this->auth_service->current_user();
		$scope = $this->warehouse_service->get_user_warehouse_scope($user);

		if ($scope !== NULL && (int) $result['data']->warehouse_id !== (int) $scope)
		{
			$this->json_error('Forbidden', 403);
			return;
		}

		$this->json_success($result['data']);
	}

	public function create()
	{
		$this->require_api_auth();
		$this->require_permission('manage_bills');

		if ($this->input->method() !== 'post')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$input = $this->get_json_input();
		$user = $this->auth_service->current_user();
		$scope = $this->warehouse_service->get_user_warehouse_scope($user);

		if ($scope !== NULL && (int) $input['warehouse_id'] !== (int) $scope)
		{
			$this->json_error('You do not have access to this warehouse', 403);
			return;
		}

		$result = $this->bill_service->create($input);
		$this->handle_service_result($result, 201);
	}

	public function delete($id)
	{
		$this->require_api_auth();
		$this->require_permission('manage_bills');

		if ($this->input->method() !== 'delete')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->bill_service->delete($id);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 404);
			return;
		}

		$this->json_success(NULL, $result['message']);
	}
}
