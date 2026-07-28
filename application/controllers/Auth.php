<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Controller.php';

class Auth extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->service('auth_service');
	}

	public function login()
	{
		if ($this->input->method() !== 'post')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$input = $this->get_json_input();
		$email = isset($input['email']) ? $input['email'] : $this->input->post('email');
		$password = isset($input['password']) ? $input['password'] : $this->input->post('password');

		$result = $this->auth_service->login($email, $password);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 401);
			return;
		}

		$this->json_success($result['data'], $result['message']);
	}

	public function logout()
	{
		$result = $this->auth_service->logout();
		$this->json_success($result['data'], $result['message']);
	}

	public function me()
	{
		$user = $this->auth_service->current_user();

		if ( ! $user)
		{
			$this->json_error('Unauthorized', 401);
			return;
		}

		$this->json_success($user);
	}
}
