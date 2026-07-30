<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Model.php';

class Product_model extends MY_Model {

	protected $table = 'products';

	public function get_available()
	{
		return $this->get_where(array('is_available' => 1));
	}

	public function get_by_code($code)
	{
		return $this->get_one_where(array('code' => $code));
	}

	public function get_product_warehouses($product_id)
	{
		return $this->db
			->select('product_warehouse.*, warehouses.name AS warehouse_name')
			->from('product_warehouse')
			->join('warehouses', 'warehouses.id = product_warehouse.warehouse_id')
			->where('product_warehouse.product_id', $product_id)
			->get()
			->result();
	}

	public function get_warehouses($product_id)
	{
		return $this->db
			->select('warehouses.*')
			->from('warehouses')
			->join('product_warehouse', 'product_warehouse.warehouse_id = warehouses.id')
			->where('product_warehouse.product_id', $product_id)
			->get()
			->result();
	}

	public function get_bill_details($product_id)
	{
		return $this->db->get_where('bill_details', array('product_id' => $product_id))->result();
	}

	public function get_paginated_with_inventory(array $params = array())
	{
		$page = max(1, (int) (isset($params['page']) ? $params['page'] : 1));
		$per_page = max(1, (int) (isset($params['per_page']) ? $params['per_page'] : 10));
		$search = isset($params['search']) ? trim($params['search']) : '';
		$warehouse_id = isset($params['warehouse_id']) ? (int) $params['warehouse_id'] : null;
		$only_available = isset($params['only_available']) ? (bool) $params['only_available'] : false;

		$this->db->from($this->table);

		if ($search !== '')
		{
			$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('code', $search);
			$this->db->group_end();
		}

		if ($only_available)
		{
			$this->db->where('is_available', 1);
		}

		if ($warehouse_id !== null)
		{
			$this->db->where("{$this->table}.id IN (SELECT product_id FROM product_warehouse WHERE warehouse_id = $warehouse_id)", NULL, FALSE);
		}

		$total = $this->db->count_all_results('', FALSE);
		$offset = ($page - 1) * $per_page;

		$items = $this->db
			->order_by('name', 'ASC')
			->limit($per_page, $offset)
			->get()
			->result();

		// Load inventory for each product
		$this->load->model('product_warehouse_model');
		foreach ($items as $item) {
			$item->inventory = $this->product_warehouse_model->get_by_product($item->id, $warehouse_id);
		}

		return array(
			'items' => $items,
			'total' => (int) $total,
			'page' => $page,
			'per_page' => $per_page,
			'total_pages' => (int) ceil($total / $per_page),
		);
	}

	public function get_paginated_for_manage(array $params = array())
	{
		$page = max(1, (int) (isset($params['page']) ? $params['page'] : 1));
		$per_page = max(1, (int) (isset($params['per_page']) ? $params['per_page'] : 10));
		$search = isset($params['search']) ? trim($params['search']) : '';
		$warehouse_id = isset($params['warehouse_id']) ? (int) $params['warehouse_id'] : null;

		$this->db->from($this->table);

		if ($search !== '')
		{
			$this->db->group_start();
			$this->db->like('name', $search);
			$this->db->or_like('code', $search);
			$this->db->group_end();
		}

		// If warehouse_id is provided, only show products in that warehouse
		if ($warehouse_id !== null)
		{
			$this->db->where("{$this->table}.id IN (SELECT product_id FROM product_warehouse WHERE warehouse_id = $warehouse_id)", NULL, FALSE);
		}

		$total = $this->db->count_all_results('', FALSE);
		$offset = ($page - 1) * $per_page;

		$items = $this->db
			->order_by('name', 'ASC')
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

	public function get_by_warehouse($warehouse_id)
	{
		return $this->db
			->select('products.*, product_warehouse.quantity, product_warehouse.alert_quantity')
			->from('products')
			->join('product_warehouse', 'product_warehouse.product_id = products.id')
			->where('product_warehouse.warehouse_id', $warehouse_id)
			->order_by('products.name', 'ASC')
			->get()
			->result();
	}
}
