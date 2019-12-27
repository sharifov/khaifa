<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Core_Controller extends CI_Controller
{

    /** @var
     * This one will hold the directory name
     */
    public $directory;

    /** @var
     * This one will hold the contraoller name
     */
    public $controller;

    /** @var
     * This one will hold the model name proper to its controller
     */
    public $model;

    /** @var
     * This one will hold the requested method
     */
    public $method;

    /** @var
     * This one will hold the theme
     */
    public $theme;

    /** @var
     * This one will hold the module name
     */
    public $module_name;


    public function __construct()
    {
        parent::__construct();

        $this->directory = $this->router->directory;
        $this->controller = $this->router->class;
        $this->method = $this->router->method;
        $this->model = ucfirst($this->controller) . "_model";

        //Gets the list of active languages
        $languages = $this->Language_model->filter(['status' => 1])->order_by('sort', 'ASC')->all();

        if ($languages) {
            foreach ($languages as $language) {
                $method = '';
                if($this->method != 'index')
                {
                    $method = $this->method;
                }

                $controller = $this->controller != 'home' ? $this->controller: '';
                $methodName = $method ? '/' . $method : '';
				
                $this->data['languages'][$language->slug] = [
                    'id' => $language->id,
                    'name' => $language->name,
                    'code' => $language->code,
                    'slug' => $language->slug,
                    'admin' => $language->admin,
                    'directory' => $language->directory,
                    'dir' => $language->direction,
                    'link' => base_url($language->slug.'/'.($this->directory.$controller.$methodName))
                ];


                if ($language->default == 1) {
                    $this->data['default_language'] = $language->slug;
                }
            }
        }
		

        //Checks url for the language slug and sets it or sets default language
		$_ses_lang = $this->session->userdata('current_lang');

		$lang_slug = ($this->uri->segment(1) && strlen($this->uri->segment(1)) == 2) ? $this->uri->segment(1) : ($_ses_lang ? $_ses_lang : $this->data['default_language']);

		if (isset($lang_slug) && array_key_exists($lang_slug, $this->data['languages'])) {
			$current_lang = $lang_slug;
		} else {
			$current_lang = $this->data['default_language'];
		}

		//Sets current language to user session
		$this->session->set_userdata('current_lang', $current_lang);
		$this->session->set_userdata('default_language', $this->data['default_language']);

        //Sets language's directory to config
        $this->config->set_item('language', $this->data['languages'][$current_lang]['directory']);

        $this->data['current_lang'] = $current_lang;
        $this->data['current_lang_id'] = $this->data['languages'][$current_lang]['id'];
		
        $this->data['controller'] = $this->controller;
        $this->data['method'] = $this->method;

        //Loads common Language file for per Controller
        $this->lang->load($this->directory . 'main');

        //Loads requested Controller's Language file except module controller.
        if ($this->directory != 'admin' && $this->controller != 'module') {
            $this->lang->load($this->directory . $this->controller);
        }

    }
}

require_once('Administrator_Controller.php');
require_once('Site_Controller.php');
