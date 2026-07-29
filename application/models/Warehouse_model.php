<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Model.php';

class Warehouse_model extends MY_Model {

	protected $table = 'warehouses';

	public function get_all($conditions = [], $order_by = null)
	{
		if ( ! empty($conditions))
		{
			$this->db->where($conditions);
		}

		if ($order_by !== null)
		{
			$this->db->order_by($order_by);
		}

		return $this->db->get($this->table)->result();
	}

	public function get_products($warehouse_id)
	{
		return $this->db
			->select('products.*, product_warehouse.quantity, product_warehouse.alert_quantity')
			->from('products')
			->join('product_warehouse', 'product_warehouse.product_id = products.id')
			->where('product_warehouse.warehouse_id', $warehouse_id)
			->get()
			->result();
	}

	public function get_product_warehouses($warehouse_id)
	{
		return $this->db->get_where('product_warehouse', array('warehouse_id' => $warehouse_id))->result();
	}

	public function get_users($warehouse_id)
	{
		return $this->db->get_where('users', array('warehouse_id' => $warehouse_id))->result();
	}

	public function get_bills($warehouse_id)
	{
		return $this->db
			->where('warehouse_id', $warehouse_id)
			->order_by('id', 'DESC')
			->get('bills')
			->result();
	}

	public function get_paginated(array $params = array())
	{
		$page = max(1, (int) (isset($params['page']) ? $params['page'] : 1));
		$per_page = max(1, (int) (isset($params['per_page']) ? $params['per_page'] : 10));
		$search = isset($params['search']) ? trim($params['search']) : '';

		$this->db->from($this->table);

		if ($search !== '')
		{
			$this->db->like('name', $search);
			$this->db->or_like('address', $search);
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
}
