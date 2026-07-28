<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Model.php';

class Bill_detail_model extends MY_Model {

	protected $table = 'bill_details';

	public function get_by_bill($bill_id)
	{
		return $this->get_where(array('bill_id' => $bill_id));
	}

	public function delete_by_bill($bill_id)
	{
		return $this->db->delete($this->table, array('bill_id' => $bill_id));
	}
}
