<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

	protected $table;
	protected $primary_key = 'id';

	public function get_all($conditions = array(), $order_by = NULL)
	{
		if ( ! empty($conditions))
		{
			$this->db->where($conditions);
		}

		if ($order_by !== NULL)
		{
			$this->db->order_by($order_by);
		}

		return $this->db->get($this->table)->result();
	}

	public function get_by_id($id)
	{
		return $this->db->get_where($this->table, array($this->primary_key => $id))->row();
	}

	public function get_where($conditions)
	{
		return $this->db->get_where($this->table, $conditions)->result();
	}

	public function get_one_where($conditions)
	{
		return $this->db->get_where($this->table, $conditions)->row();
	}

	public function insert($data)
	{
		$data = $this->_stamp_timestamps($data, TRUE);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($id, $data)
	{
		$data = $this->_stamp_timestamps($data, FALSE);
		$this->db->where($this->primary_key, $id);
		return $this->db->update($this->table, $data);
	}

	public function delete($id)
	{
		$this->db->where($this->primary_key, $id);
		return $this->db->delete($this->table);
	}

	public function count_all($conditions = array())
	{
		if ( ! empty($conditions))
		{
			$this->db->where($conditions);
		}

		return $this->db->count_all_results($this->table);
	}

	public function paginate($conditions = array(), $order_by = NULL, $page = 1, $per_page = 10)
	{
		$page = max(1, (int) $page);
		$per_page = max(1, (int) $per_page);
		$offset = ($page - 1) * $per_page;

		if ( ! empty($conditions))
		{
			$this->db->where($conditions);
		}

		$total = $this->db->count_all_results($this->table, FALSE);

		if ($order_by !== NULL)
		{
			$this->db->order_by($order_by);
		}

		$items = $this->db->limit($per_page, $offset)->get()->result();

		return array(
			'items' => $items,
			'total' => (int) $total,
			'page' => $page,
			'per_page' => $per_page,
			'total_pages' => (int) ceil($total / $per_page),
		);
	}

	protected function _stamp_timestamps($data, $is_insert)
	{
		$now = date('Y-m-d H:i:s');

		if ($is_insert && ! isset($data['created_at']))
		{
			$data['created_at'] = $now;
		}

		if ( ! isset($data['updated_at']))
		{
			$data['updated_at'] = $now;
		}

		return $data;
	}
}
