<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/Web_Controller.php';

class Reports extends Web_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->service('product_warehouse_service');
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

	public function low_stock()
	{
		$this->require_auth();
		$this->require_permission_web('view_reports');

		$this->render('reports/low_stock', array(
			'title' => 'Low Stock Report',
			'page_script' => 'low-stock.js',
		));
	}

	public function low_stock_data()
	{
		$this->require_api_auth();
		$this->require_permission('view_reports');

		$user = $this->auth_service->current_user();
		$scope = $this->warehouse_service->get_user_warehouse_scope($user);

		$page = (int) $this->input->get('page') ?: 1;
		$per_page = (int) $this->input->get('per_page') ?: 10;
		$search = $this->input->get('search') ?: '';

		$result = $this->product_warehouse_service->get_low_stock_paginated(
			$scope !== NULL ? $scope : NULL,
			$page,
			$per_page,
			$search
		);

		$this->json_success($result);
	}

	public function low_stock_export()
	{
		$this->require_api_auth();
		$this->require_permission('view_reports');

		$user = $this->auth_service->current_user();
		$scope = $this->warehouse_service->get_user_warehouse_scope($user);
		$search = $this->input->get('search') ?: '';

		$items = $this->product_warehouse_service->get_low_stock_all(
			$scope !== NULL ? $scope : NULL,
			$search
		);

		// Format data for CSV
		$data = array();
		foreach ($items as $item) {
			$data[] = array(
				'product_name' => $item->product_name,
				'product_code' => $item->product_code,
				'warehouse_name' => $item->warehouse_name,
				'quantity' => (int) $item->quantity,
				'alert_quantity' => (int) $item->alert_quantity,
				'needed_quantity' => (int) $item->alert_quantity - (int) $item->quantity
			);
		}

		$this->json_success($data);
	}
}
