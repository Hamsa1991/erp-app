<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'core/MY_Controller.php';

class Products extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->service('product_service');
		$this->load->service('auth_service');

		if ( ! $this->auth_service->current_user())
		{
			$this->json_error('Unauthorized', 401);
			exit;
		}
	}

	public function index()
	{
		$this->require_permission('view_products');
		$result = $this->product_service->list_all();
		$this->json_success($result['data']);
	}

	public function show($id)
	{
		$this->require_permission('view_products');
		$result = $this->product_service->get($id);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 404);
			return;
		}

		$this->json_success($result['data']);
	}

	public function create()
	{
		$this->require_permission('manage_products');

		if ($this->input->method() !== 'post')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->product_service->create($this->get_json_input());

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 422, isset($result['errors']) ? $result['errors'] : NULL);
			return;
		}

		$this->json_success($result['data'], $result['message'], 201);
	}

	public function update($id)
	{
		$this->require_permission('manage_products');

		if ( ! in_array($this->input->method(), array('put', 'patch', 'post'), TRUE))
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->product_service->update($id, $this->get_json_input());

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 422, isset($result['errors']) ? $result['errors'] : NULL);
			return;
		}

		$this->json_success($result['data'], $result['message']);
	}

	public function delete($id)
	{
		$this->require_permission('manage_products');

		if ($this->input->method() !== 'delete')
		{
			$this->json_error('Method not allowed', 405);
			return;
		}

		$result = $this->product_service->delete($id);

		if ( ! $result['success'])
		{
			$this->json_error($result['message'], 404);
			return;
		}

		$this->json_success(NULL, $result['message']);
	}
}
