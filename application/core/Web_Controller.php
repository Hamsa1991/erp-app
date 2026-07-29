<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Controller.php';

class Web_Controller extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->service('auth_service');
	}

	protected function require_auth()
	{
		if ( ! $this->auth_service->current_user())
		{
			redirect('login');
		}
	}

	protected function require_permission_web($slug)
	{
		if ( ! $this->auth_service->current_user_has_permission($slug))
		{
			show_error('Forbidden: missing permission '.$slug, 403);
		}
	}

	protected function render($view, $data = array(), $layout = 'main')
	{
		$data['current_user'] = $this->auth_service->current_user();
		$data['layout'] = $layout;
		$data['content'] = $this->load->view($view, $data, TRUE);
		$this->load->view('layouts/'.$layout, $data);
	}

	protected function handle_service_result($result, $success_code = 200, $error_code = 422)
	{
		if ($result['success'])
		{
			$this->json_success($result['data'], $result['message'], $success_code);
			return;
		}

		$this->json_error(
			$result['message'],
			$error_code,
			isset($result['errors']) ? $result['errors'] : NULL
		);
	}
}
