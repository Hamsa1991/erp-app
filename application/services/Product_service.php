<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'services/Base_service.php';

class Product_service extends Base_service {

	public function __construct()
	{
		parent::__construct();
		$this->CI->load->model('product_model');
	}

	public function list_all()
	{
		return $this->success($this->CI->product_model->get_all(array(), 'name ASC'));
	}

	public function get($id)
	{
		$product = $this->CI->product_model->get_by_id($id);

		if ( ! $product)
		{
			return $this->error('Product not found');
		}

		return $this->success($product);
	}

	public function create(array $data)
	{
		$validation = $this->validate($data);

		if ( ! $validation['success'])
		{
			return $validation;
		}

		if ($this->CI->product_model->get_by_code($data['code']))
		{
			return $this->error('Product code already exists');
		}

		$id = $this->CI->product_model->insert(array(
			'name' => $data['name'],
			'code' => $data['code'],
			'price' => $data['price'],
			'is_available' => isset($data['is_available']) ? (int) $data['is_available'] : 1,
		));

		return $this->success($this->CI->product_model->get_by_id($id), 'Product created');
	}

	public function update($id, array $data)
	{
		$product = $this->CI->product_model->get_by_id($id);

		if ( ! $product)
		{
			return $this->error('Product not found');
		}

		$validation = $this->validate($data, FALSE);

		if ( ! $validation['success'])
		{
			return $validation;
		}

		if (isset($data['code']) && $data['code'] !== $product->code)
		{
			$existing = $this->CI->product_model->get_by_code($data['code']);

			if ($existing && (int) $existing->id !== (int) $id)
			{
				return $this->error('Product code already exists');
			}
		}

		$payload = array();

		foreach (array('name', 'code', 'price', 'is_available') as $field)
		{
			if (array_key_exists($field, $data))
			{
				$payload[$field] = $field === 'is_available' ? (int) $data[$field] : $data[$field];
			}
		}

		$this->CI->product_model->update($id, $payload);

		return $this->success($this->CI->product_model->get_by_id($id), 'Product updated');
	}

	public function delete($id)
	{
		if ( ! $this->CI->product_model->get_by_id($id))
		{
			return $this->error('Product not found');
		}

		$this->CI->product_model->delete($id);
		return $this->success(NULL, 'Product deleted');
	}

	private function validate(array $data, $is_create = TRUE)
	{
		$errors = array();

		if ($is_create && empty($data['name']))
		{
			$errors['name'] = 'Name is required';
		}

		if ($is_create && empty($data['code']))
		{
			$errors['code'] = 'Code is required';
		}

		if ($is_create && ! isset($data['price']))
		{
			$errors['price'] = 'Price is required';
		}

		if (isset($data['price']) && ! is_numeric($data['price']))
		{
			$errors['price'] = 'Price must be numeric';
		}

		if ( ! empty($errors))
		{
			return $this->error('Validation failed', $errors);
		}

		return $this->success();
	}
}
