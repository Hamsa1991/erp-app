<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base_service {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->database();
	}

	protected function success($data = NULL, $message = 'Success')
	{
		return array(
			'success' => TRUE,
			'message' => $message,
			'data' => $data,
		);
	}

	protected function error($message, $errors = NULL)
	{
		$response = array(
			'success' => FALSE,
			'message' => $message,
		);

		if ($errors !== NULL)
		{
			$response['errors'] = $errors;
		}

		return $response;
	}
}
