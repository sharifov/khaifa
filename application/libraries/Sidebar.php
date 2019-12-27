<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Sidebar
{

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('Admin_menu_model');
		$this->CI->load->model('Vendor_menu_model');
		$this->CI->load->model('Extension_model');
	}

	public function getMenu($parent = 0)
	{
		
		$menus = $this->CI->Admin_menu_model->filter(['status' => 1, 'parent' => $parent])->order_by('sort', 'ASC')->all();

		if ($menus) {
			$navbar_menu = [];
			foreach ($menus as $menu) {
					$navbar_menu[] = $this->formatMenu($menu);
			}
			return $navbar_menu;
		}
		return [];
	}

	public function getMenuVendor($parent = 0)
	{
		
		$menus = $this->CI->Vendor_menu_model->filter(['status' => 1, 'parent' => $parent])->order_by('sort', 'ASC')->all();

		if ($menus) {
			$navbar_menu = [];
			foreach ($menus as $menu) {
					$navbar_menu[] = $this->formatMenu($menu);
			}
			return $navbar_menu;
		}
		return [];
	}

	public function formatMenu($menu)
	{

		$name = valid_lang(json_decode($menu->name));

		if($menu->module == 0)
		{
			if($menu->static == 1)
			{
				$menu = [
					'href'		=> site_url_multi(get_setting('admin_url').'/' . $menu->link),
					'icon'		=> $menu->icon,
					'name'		=> $name,
					'active'	=> 0,
					'target'	=> $menu->target,
					'parent'	=> $this->getMenu($menu->id)
				];
			}
			else
			{
				$link = valid_lang(json_decode($menu->link));

				$menu = [
					'href'		=> $link,
					'icon'		=> $menu->icon,
					'name'		=> $name,
					'active'	=> 0,
					'target'	=> $menu->target,
					'parent'	=> $this->getMenu($menu->id)
				];
			}
		}
		else
		{

			$module = $this->CI->Extension_model->filter(['id' => $menu->module, 'status' => 1])->one();

			$menu = [
				'href'		=> site_url_multi(get_setting('admin_url').'/' . $module->slug),
				'icon'		=> $menu->icon,
				'name'		=> $name,
				'active'	=> 0,
				'target'	=> $menu->target,
				'parent'	=> $this->getMenu($menu->id)
			];
			
		}

		return $menu;
	}

}