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
}
