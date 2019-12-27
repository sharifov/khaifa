<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once( BASEPATH.'libraries/Smarty/libs/Smarty.class.php' );

class CI_Smarty extends Smarty {
	
	public function __construct()
	{
		parent::__construct();

		$this->compile_dir = "templates_c";
		$this->template_dir = "templates";

		// Assign CodeIgniter object by reference to CI
		if ( method_exists( $this, 'assignByRef') )
		{
			$ci =& get_instance();
			$this->assignByRef("ci", $ci);
		}

		log_message('debug', "Smarty Class Initialized");
	}

	public function view($template, $data = [], $return = FALSE)
	{
		foreach ($data as $key => $val)
		{
			$this->assign($key, $val);
		}
		
		if ($return == FALSE)
		{
			$CI =& get_instance();
			if (method_exists( $CI->output, 'set_output' ))
			{
				$CI->output->set_output( $this->fetch($template) );
			}
			else
			{
				$CI->output->final_output = $this->fetch($template);
			}
			return;
		}
		else
		{
			return $this->fetch($template);
		}
	}
}