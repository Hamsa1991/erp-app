<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Loader extends CI_Loader {

	public function service($service, $object_name = NULL, $connect = FALSE)
	{
		if (empty($service))
		{
			return $this;
		}

		if (is_array($service))
		{
			foreach ($service as $key => $value)
			{
				if (is_int($key))
				{
					$this->service($value, '', $connect);
				}
				else
				{
					$this->service($key, $value, $connect);
				}
			}

			return $this;
		}

		$service = strtolower($service);
		$path = APPPATH.'services/';

		if ($object_name === NULL)
		{
			$object_name = $service;
		}

		if (isset($this->_ci_services[$object_name]))
		{
			return $this;
		}

		$class = ucfirst($service);
		$file = $path.$class.'.php';

		if ( ! file_exists($file))
		{
			show_error('Unable to load the requested service: '.$class);
		}

		require_once($file);

		$CI =& get_instance();

		if ($connect !== FALSE && ! class_exists('CI_DB', FALSE))
		{
			$CI->load->database($connect, TRUE, TRUE);
		}

		$CI->$object_name = new $class();
		$this->_ci_services[$object_name] = $class;

		return $this;
	}

	protected $_ci_services = array();
}
