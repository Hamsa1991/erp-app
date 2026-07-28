<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Model.php';

class Product_warehouse_model extends MY_Model {

	protected $table = 'product_warehouse';

	public function get_by_product_and_warehouse($product_id, $warehouse_id)
	{
		return $this->get_one_where(array(
			'product_id' => $product_id,
			'warehouse_id' => $warehouse_id,
		));
	}

	public function get_by_warehouse($warehouse_id)
	{
		return $this->db
			->select('product_warehouse.*, products.name AS product_name, products.code AS product_code')
			->from($this->table)
			->join('products', 'products.id = product_warehouse.product_id')
			->where('product_warehouse.warehouse_id', $warehouse_id)
			->get()
			->result();
	}

	public function get_low_stock($warehouse_id = NULL)
	{
		$this->db
			->select('product_warehouse.*, products.name AS product_name, products.code AS product_code, warehouses.name AS warehouse_name')
			->from($this->table)
			->join('products', 'products.id = product_warehouse.product_id')
			->join('warehouses', 'warehouses.id = product_warehouse.warehouse_id')
			->where('product_warehouse.quantity <= product_warehouse.alert_quantity');

		if ($warehouse_id !== NULL)
		{
			$this->db->where('product_warehouse.warehouse_id', $warehouse_id);
		}

		return $this->db->get()->result();
	}
}
