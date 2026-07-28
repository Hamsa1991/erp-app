<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Controller.php';

class Product_warehouse extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->service('product_warehouse_service');
		$this->load->service('auth_service');

		if ( ! $this->auth_service->current_user())
		{
			$this->json_error('Unauthorized', 401);
			exit;
		}
	}

	public function index()
	{
		$this->require_permission('manage_inventory');
		$warehouse_id = $this->input->get('warehouse_id');
		$result = $this->product_warehouse_service->list_all($warehouse_id ?: NULL);
		$this->json_success($result['data']);
	}

	public function low_stock()
	{
		$this->require_permission('manage_inventory');
		$warehouse_id = $this->input->get('warehouse_id');
		$result = $this->product_warehouse_service->low_stock($warehouse_id ?: NULL);
		$this->json_success($result['data']);
	}

	public function show($id)
	{
		$this->require_permission('manage_inventory');
		$result = $this->product_warehouse_service->get($id);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 404);
			return;
		}

		$this->json_success($result['data']);
	}

	public function create()
	{
		$this->require_permission('manage_inventory');

		if ($this->input->method() !== 'post')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->product_warehouse_service->create($this->get_json_input());

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 422, isset($result['errors']) ? $result['errors'] : NULL);
			return;
		}

		$this->json_success($result['data'], $result['message'], 201);
	}

	public function update($id)
	{
		$this->require_permission('manage_inventory');

		if ( ! in_array($this->input->method(), array('put', 'patch', 'post'), TRUE))
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->product_warehouse_service->update($id, $this->get_json_input());

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 422, isset($result['errors']) ? $result['errors'] : NULL);
			return;
		}

		$this->json_success($result['data'], $result['message']);
	}
}
