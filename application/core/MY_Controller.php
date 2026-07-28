<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	protected function json_response($data, $status_code = 200)
	{
		$this->output
			->set_status_header($status_code)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	protected function json_success($data = NULL, $message = 'Success', $status_code = 200)
	{
		$this->json_response(array(
			'success' => TRUE,
			'message' => $message,
			'data' => $data,
		), $status_code);
	}

	protected function json_error($message, $status_code = 400, $errors = NULL)
	{
		$response = array(
			'success' => FALSE,
			'message' => $message,
		);

		if ($errors !== NULL)
		{
			$response['errors'] = $errors;
		}

		$this->json_response($response, $status_code);
	}

	protected function get_json_input()
	{
		$input = json_decode($this->input->raw_input_stream, TRUE);
		return is_array($input) ? $input : array();
	}

	protected function require_permission($slug)
	{
		$this->load->service('auth_service');

		if ( ! $this->auth_service->current_user_has_permission($slug))
		{
			$this->json_error('Forbidden: missing permission '.$slug, 403);
			exit;
		}
	}
}
