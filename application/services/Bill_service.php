<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'services/Base_service.php';

class Bill_service extends Base_service {

	public function __construct()
	{
		parent::__construct();
		$this->CI->load->model('bill_model');
		$this->CI->load->model('bill_detail_model');
		$this->CI->load->model('client_model');
		$this->CI->load->model('warehouse_model');
		$this->CI->load->model('product_model');
		$this->CI->load->model('product_warehouse_model');
		$this->CI->load->service('warehouse_service');
	}

	public function list_all($warehouse_id = NULL)
	{
		if ($warehouse_id !== NULL)
		{
			return $this->list_paginated(array('warehouse_id' => $warehouse_id, 'per_page' => 1000));
		}

		return $this->success($this->CI->bill_model->get_all_with_relations());
	}

	public function list_paginated(array $params = array())
	{
		$result = $this->CI->bill_model->get_paginated($params);
		return $this->success($result);
	}

	public function get($id)
	{
		$bill = $this->CI->bill_model->get_with_details($id);

		if ( ! $bill)
		{
			return $this->error('Bill not found');
		}

		return $this->success($bill);
	}

	public function create(array $data)
	{
		$validation = $this->validate($data);

		if ( ! $validation['success'])
		{
			return $validation;
		}

		$this->CI->db->trans_start();

		$total = 0;
		$details = array();

		foreach ($data['details'] as $line)
		{
			$product = $this->CI->product_model->get_by_id($line['product_id']);

			if ( ! $product)
			{
				$this->CI->db->trans_complete();
				return $this->error('Invalid product in bill details');
			}

			$quantity = (int) $line['quantity'];
			$price = isset($line['price']) ? (float) $line['price'] : (float) $product->price;
			$line_total = $quantity * $price;
			$total += $line_total;

			$stock = $this->CI->product_warehouse_model->get_by_product_and_warehouse(
				$line['product_id'],
				$data['warehouse_id']
			);

			// Check if stock exists and if quantity is available
			if ( ! $stock)
			{
				$this->CI->db->trans_complete();
				return $this->error('Product not found in warehouse: '.$product->name);
			}

			// Validate that the entered quantity is not more than available stock
			if ((int) $stock->quantity < $quantity)
			{
				$this->CI->db->trans_complete();
				return $this->error('Insufficient stock for product: '.$product->name.'. Available: '.(int)$stock->quantity.', Requested: '.$quantity);
			}

			$details[] = array(
				'product_id' => (int) $line['product_id'],
				'quantity' => $quantity,
				'price' => $price,
				'stock_id' => (int) $stock->id,
				'stock_quantity' => (int) $stock->quantity,
			);
		}

		// Validate discount is a percentage between 0 and 100
		$discount_percent = isset($data['discount']) ? (float) $data['discount'] : 0;

		// Validate discount is between 0 and 100
		if ($discount_percent < 0 || $discount_percent > 100)
		{
			$this->CI->db->trans_complete();
			return $this->error('Discount must be a percentage between 0 and 100');
		}

		// Calculate discount amount and round the result
		$discount_amount = round(($total * $discount_percent) / 100, 2);
		$total_after_discount = round(max(0, $total - $discount_amount), 2);

		$bill_id = $this->CI->bill_model->insert(array(
			'client_id' => (int) $data['client_id'],
			'warehouse_id' => (int) $data['warehouse_id'],
			'total' => round($total, 2),
			'discount' => $discount_amount,
			'total_after_discount' => $total_after_discount
		));

		foreach ($details as $detail)
		{
			$stock_id = $detail['stock_id'];
			$stock_quantity = $detail['stock_quantity'];
			unset($detail['stock_id'], $detail['stock_quantity']);

			$detail['bill_id'] = $bill_id;
			$this->CI->bill_detail_model->insert($detail);

			$this->CI->product_warehouse_model->update($stock_id, array(
				'quantity' => $stock_quantity - $detail['quantity'],
			));
		}

		$this->CI->db->trans_complete();

		if ($this->CI->db->trans_status() === FALSE)
		{
			return $this->error('Failed to create bill');
		}

		// Return the bill data with a redirect URL
		$bill_data = $this->CI->bill_model->get_with_details($bill_id);

		// Add redirect URL to the response
		$bill_data->redirect_url = '/index.php/bills/detail/'.$bill_id;

		return $this->success($bill_data, 'Bill created');
	}

	public function delete($id)
	{
		if ( ! $this->CI->bill_model->get_by_id($id))
		{
			return $this->error('Bill not found');
		}

		$this->CI->bill_model->delete($id);
		return $this->success(NULL, 'Bill deleted');
	}

	private function validate(array $data)
	{
		$errors = array();

		if (empty($data['client_id']) || ! $this->CI->client_model->get_by_id($data['client_id']))
		{
			$errors['client_id'] = 'Valid client is required';
		}

		if (empty($data['warehouse_id']) || ! $this->CI->warehouse_model->get_by_id($data['warehouse_id']))
		{
			$errors['warehouse_id'] = 'Valid warehouse is required';
		}

		if (empty($data['details']) || ! is_array($data['details']))
		{
			$errors['details'] = 'At least one bill detail is required';
		}

		// Validate discount is a percentage between 0 and 100
		if (isset($data['discount']))
		{
			$discount = (float) $data['discount'];
			if ($discount < 0 || $discount > 100)
			{
				$errors['discount'] = 'Discount must be between 0 and 100 percent';
			}
		}

		if ( ! empty($errors))
		{
			return $this->error('Validation failed', $errors);
		}

		return $this->success();
	}
}
