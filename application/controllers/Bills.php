<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Controller.php';

class Bills extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->service('bill_service');
		$this->load->service('auth_service');

		if ( ! $this->auth_service->current_user())
		{
			$this->json_error('Unauthorized', 401);
			exit;
		}
	}

	public function index()
	{
		$this->require_permission('view_bills');
		$result = $this->bill_service->list_all();
		$this->json_success($result['data']);
	}

	public function show($id)
	{
		$this->require_permission('view_bills');
		$result = $this->bill_service->get($id);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 404);
			return;
		}

		$this->json_success($result['data']);
	}

	public function create()
	{
		$this->require_permission('manage_bills');

		if ($this->input->method() !== 'post')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->bill_service->create($this->get_json_input());

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 422, isset($result['errors']) ? $result['errors'] : NULL);
			return;
		}

		$this->json_success($result['data'], $result['message'], 201);
	}

	public function delete($id)
	{
		$this->require_permission('manage_bills');

		if ($this->input->method() !== 'delete')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->bill_service->delete($id);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 404);
			return;
		}

		$this->json_success(NULL, $result['message']);
	}
}
