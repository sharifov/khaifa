<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Page extends Site_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('modules/Page_model');
	}

	public function index($slug = false)
	{

		if($slug)
		{

            redirect(site_url_multi($slug), 'auto', 301);
            die;

			$slug = urldecode($slug);
			$page = $this->Page_model->filter(['slug' => $slug])->with_translation()->one();
			if($page)
			{

				//Language Link

				foreach($this->data['languages'] as $key => $value)
				{	
					$temp_slug = $this->Page_model->filter(['id' => $page->id, 'status' => 1])->with_translation($value['id'])->one();
					$link = ($temp_slug) ? site_url($key.'/'.$temp_slug->slug) : "/";
					$this->data['languages'][$key] = [
						'id' => $value['id'],
						'name' => $value['name'],
						'code' => $value['code'],
						'slug' => $value['slug'],
						'admin' => $value['admin'],
						'directory' => $value['directory'],
						'dir' => $value['dir'],
						'link' => $link
					];				
				}

				$this->data['title'] = $page->name;
				$this->data['image'] = $page->image;
				$this->data['description'] = $page->description;
				$this->template->render('page');
			}
			else
			{
				show_404();
			}
		}
		else
		{
			show_404();
		}
	}
}