<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Model.php';

class Client_model extends MY_Model {

	protected $table = 'clients';

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
}
