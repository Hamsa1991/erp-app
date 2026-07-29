<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Model.php';

class Client_model extends MY_Model {

	protected $table = 'clients';

	public function get_bills($client_id)
	{
		return $this->db
			->where('client_id', $client_id)
			->order_by('id', 'DESC')
			->get('bills')
			->result();
	}

	public function search($term)
	{
		$this->db->group_start();
		$this->db->like('first_name', $term);
		$this->db->or_like('last_name', $term);
		$this->db->or_like('email', $term);
		$this->db->or_like('phone', $term);
		$this->db->group_end();

		return $this->db->get($this->table)->result();
	}

	public function search_paginated($term, $limit, $offset)
	{
		$this->db->group_start();
		$this->db->like('first_name', $term);
		$this->db->or_like('last_name', $term);
		$this->db->or_like('email', $term);
		$this->db->or_like('phone', $term);
		$this->db->group_end();
		$this->db->order_by('last_name', 'ASC');
		$this->db->limit($limit, $offset);

		return $this->db->get($this->table)->result();
	}

	public function count_all($search = NULL)
	{
		if ($search) {
			$this->db->group_start();
			$this->db->like('first_name', $search);
			$this->db->or_like('last_name', $search);
			$this->db->or_like('email', $search);
			$this->db->or_like('phone', $search);
			$this->db->group_end();
		}

		return $this->db->count_all_results($this->table);
	}

	public function get_all_paginated($where = array(), $order_by = NULL, $limit = NULL, $offset = 0)
	{
		if ( ! empty($where))
		{
			$this->db->where($where);
		}

		if ($order_by !== NULL)
		{
			$this->db->order_by($order_by);
		}

		if ($limit !== NULL)
		{
			$this->db->limit($limit, $offset);
		}

		return $this->db->get($this->table)->result();
	}
}
