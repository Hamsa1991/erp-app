<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$this->load->service('auth_service');

		if ($this->auth_service->current_user())
		{
			redirect('products');
		}

		redirect('login');
	}
}
