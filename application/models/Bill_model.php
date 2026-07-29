<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Model.php';

class Bill_model extends MY_Model {

	protected $table = 'bills';

	public function get_client($bill_id)
	{
		$bill = $this->get_by_id($bill_id);

		if ( ! $bill)
		{
			return NULL;
		}

		return $this->db->get_where('clients', array('id' => $bill->client_id))->row();
	}

	public function get_warehouse($bill_id)
	{
		$bill = $this->get_by_id($bill_id);

		if ( ! $bill)
		{
			return NULL;
		}

		return $this->db->get_where('warehouses', array('id' => $bill->warehouse_id))->row();
	}

	public function get_details($bill_id)
	{
		return $this->db
			->select('bill_details.*, products.name AS product_name, products.code AS product_code')
			->from('bill_details')
			->join('products', 'products.id = bill_details.product_id')
			->where('bill_details.bill_id', $bill_id)
			->get()
			->result();
	}

	public function get_with_details($id)
	{
		$bill = $this->db
			->select('bills.*, clients.first_name AS client_first_name, clients.last_name AS client_last_name, clients.email AS client_email, clients.phone AS client_phone, warehouses.name AS warehouse_name, warehouses.address AS warehouse_address')
			->from($this->table)
			->join('clients', 'clients.id = bills.client_id')
			->join('warehouses', 'warehouses.id = bills.warehouse_id')
			->where('bills.id', $id)
			->get()
			->row();

		if ( ! $bill)
		{
			return NULL;
		}

		$bill->details = $this->get_details($id);

		return $bill;
	}

	public function get_all_with_relations()
	{
		return $this->db
			->select('bills.*, clients.first_name AS client_first_name, clients.last_name AS client_last_name, warehouses.name AS warehouse_name')
			->from($this->table)
			->join('clients', 'clients.id = bills.client_id')
			->join('warehouses', 'warehouses.id = bills.warehouse_id')
			->order_by('bills.id', 'DESC')
			->get()
			->result();
	}

	public function get_paginated(array $params = array())
	{
		$page = max(1, (int) (isset($params['page']) ? $params['page'] : 1));
		$per_page = max(1, (int) (isset($params['per_page']) ? $params['per_page'] : 10));
		$warehouse_id = isset($params['warehouse_id']) ? $params['warehouse_id'] : NULL;

		$this->db
			->select('bills.*, clients.first_name AS client_first_name, clients.last_name AS client_last_name, warehouses.name AS warehouse_name')
			->from($this->table)
			->join('clients', 'clients.id = bills.client_id')
			->join('warehouses', 'warehouses.id = bills.warehouse_id');

		if ($warehouse_id !== NULL && $warehouse_id !== '')
		{
			$this->db->where('bills.warehouse_id', (int) $warehouse_id);
		}

		$total = $this->db->count_all_results('', FALSE);
		$offset = ($page - 1) * $per_page;

		$items = $this->db
			->order_by('bills.id', 'DESC')
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
