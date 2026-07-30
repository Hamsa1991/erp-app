<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/Web_Controller.php';

class Auth extends Web_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function login_page()
	{
		// Load the URL helper
		$this->load->helper('url');
		if ($this->auth_service->current_user())
		{
			redirect('products');
		}

		$this->load->view('auth/login', array(
			'title' => 'Login',
			'error' => $this->session->flashdata('login_error'),
		));
	}

	public function login()
	{
		try {
			if ($this->input->method() === 'post' && !$this->input->is_ajax_request()) {
				$email = $this->input->post('email');
				$password = $this->input->post('password');
				// Validate required fields
				if (empty($email) || empty($password)) {
					if ($this->input->is_ajax_request()) {
						$this->json_error('Email and password are required', 400);
					} else {
						$this->session->set_flashdata('login_error', 'Email and password are required');
						redirect('login');
					}
					return;
				}
				$result = $this->auth_service->login($email, $password);

				if ($result['success']) {
					redirect('products');
				}

				$this->session->set_flashdata('login_error', $result['message']);
				redirect('login');
				return;
			}

			if ($this->input->method() !== 'post') {
				$this->json_error('Method not allowed', 405);
				return;
			}

			$input = $this->get_json_input();
			$email = isset($input['email']) ? $input['email'] : $this->input->post('email');
			$password = isset($input['password']) ? $input['password'] : $this->input->post('password');

			$result = $this->auth_service->login($email, $password);

			if (!$result['success']) {
				$this->json_error($result['message'], 401);
				return;
			}

			$this->json_success($result['data'], $result['message']);
		} catch (Exception $e) {
			// Log the exception
			log_message('error', 'Login error: ' . $e->getMessage());

			if ($this->input->is_ajax_request()) {
				$this->json_error('An unexpected error occurred', 500);
			} else {
				$this->session->set_flashdata('login_error', 'An unexpected error occurred');
				redirect('login');
			}
		}
	}

	public function logout_page()
	{
		$this->auth_service->logout();
		redirect('login');
	}

	public function logout()
	{
		$result = $this->auth_service->logout();

		if ( ! $this->input->is_ajax_request())
		{
			redirect('login');
			return;
		}

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
