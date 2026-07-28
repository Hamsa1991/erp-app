<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Model.php';

class Bill_model extends MY_Model {

	protected $table = 'bills';

	public function get_with_details($id)
	{
		$bill = $this->db
			->select('bills.*, clients.first_name AS client_first_name, clients.last_name AS client_last_name, warehouses.name AS warehouse_name')
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

		$bill->details = $this->db
			->select('bill_details.*, products.name AS product_name, products.code AS product_code')
			->from('bill_details')
			->join('products', 'products.id = bill_details.product_id')
			->where('bill_details.bill_id', $id)
			->get()
			->result();

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
}
