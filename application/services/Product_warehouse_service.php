<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'services/Base_service.php';

class Product_warehouse_service extends Base_service {

	public function __construct()
	{
		parent::__construct();
		$this->CI->load->model('product_warehouse_model');
		$this->CI->load->model('product_model');
		$this->CI->load->model('warehouse_model');
		$this->CI->load->service('warehouse_service');
	}

	public function list_all($warehouse_id = NULL)
	{
		if ($warehouse_id !== NULL)
		{
			return $this->success($this->CI->product_warehouse_model->get_by_warehouse($warehouse_id));
		}

		return $this->success($this->CI->product_warehouse_model->get_all());
	}

	public function list_for_user($user, $warehouse_id = NULL)
	{
		$scope = $this->CI->warehouse_service->get_user_warehouse_scope($user);

		if ($scope === FALSE)
		{
			return $this->success(array());
		}

		if ($scope !== NULL)
		{
			return $this->list_all($scope);
		}

		return $this->list_all($warehouse_id);
	}

	public function get($id)
	{
		$row = $this->CI->product_warehouse_model->get_by_id($id);

		if ( ! $row)
		{
			return $this->error('Inventory record not found');
		}

		return $this->success($row);
	}

	public function create(array $data, $user = NULL)
	{
		if ($user !== NULL && ! $this->CI->warehouse_service->user_can_access($user, $data['warehouse_id']))
		{
			return $this->error('You do not have access to this warehouse');
		}

		$validation = $this->validate($data);

		if ( ! $validation['success'])
		{
			return $validation;
		}

		if ($this->CI->product_warehouse_model->get_by_product_and_warehouse($data['product_id'], $data['warehouse_id']))
		{
			return $this->error('Inventory record already exists for this product and warehouse');
		}

		$id = $this->CI->product_warehouse_model->insert(array(
			'product_id' => (int) $data['product_id'],
			'warehouse_id' => (int) $data['warehouse_id'],
			'quantity' => isset($data['quantity']) ? (int) $data['quantity'] : 0,
			'alert_quantity' => isset($data['alert_quantity']) ? (int) $data['alert_quantity'] : 0,
		));

		return $this->success($this->CI->product_warehouse_model->get_by_id($id), 'Inventory record created');
	}

	public function update($id, array $data, $user = NULL)
	{
		$row = $this->CI->product_warehouse_model->get_by_id($id);

		if ( ! $row)
		{
			return $this->error('Inventory record not found');
		}

		if ($user !== NULL && ! $this->CI->warehouse_service->user_can_access($user, $row->warehouse_id))
		{
			return $this->error('You do not have access to this warehouse');
		}

		$payload = array();

		foreach (array('quantity', 'alert_quantity') as $field)
		{
			if (array_key_exists($field, $data))
			{
				$payload[$field] = (int) $data[$field];
			}
		}

		$this->CI->product_warehouse_model->update($id, $payload);

		return $this->success($this->CI->product_warehouse_model->get_by_id($id), 'Inventory record updated');
	}

	public function upsert(array $data, $user = NULL)
	{
		if ($user !== NULL && ! $this->CI->warehouse_service->user_can_access($user, $data['warehouse_id']))
		{
			return $this->error('You do not have access to this warehouse');
		}

		$existing = $this->CI->product_warehouse_model->get_by_product_and_warehouse(
			$data['product_id'],
			$data['warehouse_id']
		);

		if ($existing)
		{
			return $this->update($existing->id, $data, $user);
		}

		return $this->create($data, $user);
	}

	public function adjust_quantity($product_id, $warehouse_id, $quantity_delta)
	{
		$row = $this->CI->product_warehouse_model->get_by_product_and_warehouse($product_id, $warehouse_id);

		if ( ! $row)
		{
			return $this->error('Inventory record not found');
		}

		$new_quantity = (int) $row->quantity + (int) $quantity_delta;

		if ($new_quantity < 0)
		{
			return $this->error('Insufficient stock');
		}

		$this->CI->product_warehouse_model->update($row->id, array('quantity' => $new_quantity));

		return $this->success($this->CI->product_warehouse_model->get_by_id($row->id), 'Stock adjusted');
	}

	public function low_stock($warehouse_id = NULL)
	{
		return $this->success($this->CI->product_warehouse_model->get_low_stock($warehouse_id));
	}

	private function validate(array $data)
	{
		$errors = array();

		if (empty($data['product_id']) || ! $this->CI->product_model->get_by_id($data['product_id']))
		{
			$errors['product_id'] = 'Valid product is required';
		}

		if (empty($data['warehouse_id']) || ! $this->CI->warehouse_model->get_by_id($data['warehouse_id']))
		{
			$errors['warehouse_id'] = 'Valid warehouse is required';
		}

		if ( ! empty($errors))
		{
			return $this->error('Validation failed', $errors);
		}

		return $this->success();
	}
	/**
	 * Get low stock items with pagination
	 */
	public function get_low_stock_paginated($warehouse_id = NULL, $page = 1, $per_page = 10, $search = '')
	{
		$offset = ($page - 1) * $per_page;

		$this->CI->db
			->select('
            products.id as product_id,
            products.name as product_name,
            products.code as product_code,
            warehouses.id as warehouse_id,
            warehouses.name as warehouse_name,
            product_warehouse.quantity,
            product_warehouse.alert_quantity
        ')
			->from('product_warehouse')
			->join('products', 'products.id = product_warehouse.product_id')
			->join('warehouses', 'warehouses.id = product_warehouse.warehouse_id')
			->where('product_warehouse.quantity < product_warehouse.alert_quantity')
			->where('products.is_available', 1);

		if ($warehouse_id !== NULL && $warehouse_id !== '') {
			$this->CI->db->where('product_warehouse.warehouse_id', (int) $warehouse_id);
		}

		if ($search !== '') {
			$this->CI->db->group_start();
			$this->CI->db->like('products.name', $search);
			$this->CI->db->or_like('products.code', $search);
			$this->CI->db->group_end();
		}

		$total = $this->CI->db->count_all_results('', FALSE);

		$items = $this->CI->db
			->order_by('products.name', 'ASC')
			->order_by('warehouses.name', 'ASC')
			->limit($per_page, $offset)
			->get()
			->result();

		return array(
			'items' => $items,
			'total' => (int) $total,
			'page' => $page,
			'per_page' => $per_page,
			'total_pages' => (int) ceil($total / $per_page),
		);
	}

	/**
	 * Get all low stock items for export
	 */
	public function get_low_stock_all($warehouse_id = NULL, $search = '')
	{
		$this->CI->db
			->select('
            products.name as product_name,
            products.code as product_code,
            warehouses.name as warehouse_name,
            product_warehouse.quantity,
            product_warehouse.alert_quantity
        ')
			->from('product_warehouse')
			->join('products', 'products.id = product_warehouse.product_id')
			->join('warehouses', 'warehouses.id = product_warehouse.warehouse_id')
			->where('product_warehouse.quantity < product_warehouse.alert_quantity')
			->where('products.is_available', 1);

		if ($warehouse_id !== NULL && $warehouse_id !== '') {
			$this->CI->db->where('product_warehouse.warehouse_id', (int) $warehouse_id);
		}

		if ($search !== '') {
			$this->CI->db->group_start();
			$this->CI->db->like('products.name', $search);
			$this->CI->db->or_like('products.code', $search);
			$this->CI->db->group_end();
		}

		return $this->CI->db
			->order_by('products.name', 'ASC')
			->order_by('warehouses.name', 'ASC')
			->get()
			->result();
	}
}
