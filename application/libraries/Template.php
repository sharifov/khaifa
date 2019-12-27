<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Smarty/libs/Smarty.class.php');

class Template extends Smarty
{

    protected $template_dir = 'templates';
    protected $compile_dir = 'templates_c';


    protected $CI;

    public function __construct()
    {
        parent::__construct();
        $this->CI =& get_instance();

        // Assign CodeIgniter object by reference to CI
        if (method_exists($this, 'assignByRef')) {
            $this->assignByRef("ci", $this->CI);
        }

        log_message('debug', "Smarty Class Initialized");
    }

    public function view($template, $data = [], $return = false)
    {
        foreach ($data as $key => $value) {
            $this->assign($key, $value);
        }

        if ($return == false) {
            if (method_exists($this->CI->output, 'set_output')) {
                $this->CI->output->set_output($this->fetch($template));
            } else {
                $this->CI->output->final_output = $this->fetch($template);
            }
        } else {
            return $this->fetch($template);
        }
    }

    public function render($template = false, $layout = false)
    {
        $this->CI->data['breadcrumbs'] = $this->CI->breadcrumbs->show();
        if ($layout) {
            $this->CI->data['layout'] = $this->template_dir . '/' . $this->CI->directory . $this->CI->theme . '/layout/' . $layout . '.tpl';
        } else {
            $this->CI->data['layout'] = $this->template_dir . '/' . $this->CI->directory . $this->CI->theme . '/layout/default.tpl';
        }


        if ($template) {
            $tpl = $this->template_dir . '/' . $this->CI->directory . $this->CI->theme . '/' . $template;
        } else {
            $tpl = $this->template_dir . '/' . $this->CI->directory . $this->CI->theme . '/' . $this->CI->controller . '/' . $this->CI->method;
        }


        $this->view($tpl . '.tpl', $this->CI->data);

    }


    public function json($data = [])
    {
        $this->CI->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}