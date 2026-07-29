<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'services/Base_service.php';

class Product_warehouse_service extends Base_service {

	public function __construct()
	{
		parent::__construct();
		$this->CI->load->model('product_warehouse_model');
		$this->CI->load->model('product_model');
		$this->CI->load->model('warehouse_model');
		$this->CI->load->service('warehouse_service');
	}

	public function list_all($warehouse_id = NULL)
	{
		if ($warehouse_id !== NULL)
		{
			return $this->success($this->CI->product_warehouse_model->get_by_warehouse($warehouse_id));
		}

		return $this->success($this->CI->product_warehouse_model->get_all());
	}

	public function list_for_user($user, $warehouse_id = NULL)
	{
		$scope = $this->CI->warehouse_service->get_user_warehouse_scope($user);

		if ($scope === FALSE)
		{
			return $this->success(array());
		}

		if ($scope !== NULL)
		{
			return $this->list_all($scope);
		}

		return $this->list_all($warehouse_id);
	}

	public function get($id)
	{
		$row = $this->CI->product_warehouse_model->get_by_id($id);

		if ( ! $row)
		{
			return $this->error('Inventory record not found');
		}

		return $this->success($row);
	}

	public function create(array $data, $user = NULL)
	{
		if ($user !== NULL && ! $this->CI->warehouse_service->user_can_access($user, $data['warehouse_id']))
		{
			return $this->error('You do not have access to this warehouse');
		}

		$validation = $this->validate($data);

		if ( ! $validation['success'])
		{
			return $validation;
		}

		if ($this->CI->product_warehouse_model->get_by_product_and_warehouse($data['product_id'], $data['warehouse_id']))
		{
			return $this->error('Inventory record already exists for this product and warehouse');
		}

		$id = $this->CI->product_warehouse_model->insert(array(
			'product_id' => (int) $data['product_id'],
			'warehouse_id' => (int) $data['warehouse_id'],
			'quantity' => isset($data['quantity']) ? (int) $data['quantity'] : 0,
			'alert_quantity' => isset($data['alert_quantity']) ? (int) $data['alert_quantity'] : 0,
		));

		return $this->success($this->CI->product_warehouse_model->get_by_id($id), 'Inventory record created');
	}

	public function update($id, array $data, $user = NULL)
	{
		$row = $this->CI->product_warehouse_model->get_by_id($id);

		if ( ! $row)
		{
			return $this->error('Inventory record not found');
		}

		if ($user !== NULL && ! $this->CI->warehouse_service->user_can_access($user, $row->warehouse_id))
		{
			return $this->error('You do not have access to this warehouse');
		}

		$payload = array();

		foreach (array('quantity', 'alert_quantity') as $field)
		{
			if (array_key_exists($field, $data))
			{
				$payload[$field] = (int) $data[$field];
			}
		}

		$this->CI->product_warehouse_model->update($id, $payload);

		return $this->success($this->CI->product_warehouse_model->get_by_id($id), 'Inventory record updated');
	}

	public function upsert(array $data, $user = NULL)
	{
		if ($user !== NULL && ! $this->CI->warehouse_service->user_can_access($user, $data['warehouse_id']))
		{
			return $this->error('You do not have access to this warehouse');
		}

		$existing = $this->CI->product_warehouse_model->get_by_product_and_warehouse(
			$data['product_id'],
			$data['warehouse_id']
		);

		if ($existing)
		{
			return $this->update($existing->id, $data, $user);
		}

		return $this->create($data, $user);
	}

	public function adjust_quantity($product_id, $warehouse_id, $quantity_delta)
	{
		$row = $this->CI->product_warehouse_model->get_by_product_and_warehouse($product_id, $warehouse_id);

		if ( ! $row)
		{
			return $this->error('Inventory record not found');
		}

		$new_quantity = (int) $row->quantity + (int) $quantity_delta;

		if ($new_quantity < 0)
		{
			return $this->error('Insufficient stock');
		}

		$this->CI->product_warehouse_model->update($row->id, array('quantity' => $new_quantity));

		return $this->success($this->CI->product_warehouse_model->get_by_id($row->id), 'Stock adjusted');
	}

	public function low_stock($warehouse_id = NULL)
	{
		return $this->success($this->CI->product_warehouse_model->get_low_stock($warehouse_id));
	}

	private function validate(array $data)
	{
		$errors = array();

		if (empty($data['product_id']) || ! $this->CI->product_model->get_by_id($data['product_id']))
		{
			$errors['product_id'] = 'Valid product is required';
		}

		if (empty($data['warehouse_id']) || ! $this->CI->warehouse_model->get_by_id($data['warehouse_id']))
		{
			$errors['warehouse_id'] = 'Valid warehouse is required';
		}

		if ( ! empty($errors))
		{
			return $this->error('Validation failed', $errors);
		}

		return $this->success();
	}
}
