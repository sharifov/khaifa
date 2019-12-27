<?php defined('BASEPATH') or exit('No direct script access allowed');

class Filemanager extends Administrator_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index($type = false)
	{
		$this->data['title'] 		= translate('index_title');
		$this->data['subtitle'] 	= translate('index_description');

		$filter_name = $this->input->get('filter_name');
		if (isset($filter_name)) {
			$filter_name = rtrim(str_replace('*', '', $filter_name), '/');
		} else {
			$filter_name = null;
		}
		// Make sure we have the correct directory
		$directory = $this->input->get('directory');
		if (isset($directory)) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace('*', '', $directory), '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		$page = $this->input->get('page');
		if (isset($page)) {
			$page = $page;
		} else {
			$page = 1;
		}

		$this->data['images'] = [];
		
		// Get directories
		$directories = [];
		$directories = glob($directory . '/' . $filter_name . '*', GLOB_ONLYDIR);

		// Get files
		$files = [];
		$files = glob($directory . '/' . $filter_name . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF,PDF,pdf,doc,docx,xls,xlsx,DOC,DOCX,XLS,XLSX}', GLOB_BRACE);

		array_multisort(
			array_map( 'filemtime', $files ),
			SORT_NUMERIC,
			SORT_DESC,
			$files
		);

		// Merge directories and files
		$images = array_merge($directories, $files);


		// Get total number of files and directories
		$image_total = count($images);

		// Split the array based on current page number and max number of items per page of 10
		$images = array_splice($images, ($page - 1) * 16, 16);

		foreach ($images as $image) {
			$name = basename($image);

			if (is_dir($image)) {
				$url = '';

				$this->data['target'] = $this->input->get('target');
				if (isset($this->data['target'])) {
					$url .= '&target=' . $this->data['target'];
				}
				$thumb = $this->input->get('thumb');
				if (isset($thumb)) {
					$url .= '&thumb=' . $thumb;
				}

				if ($type == 'popup') {
					$folder_href = site_url_multi($this->admin_url.'/filemanager/index/popup').'?directory=' .substr($image, strlen(DIR_IMAGE . 'catalog/')) . $url;
				} elseif ($type == 'ckeditor') {
					$folder_href = site_url_multi($this->admin_url.'/filemanager/index/ckeditor').'?directory=' .substr($image, strlen(DIR_IMAGE . 'catalog/')) . $url;
				} else {
					$folder_href = site_url_multi($this->admin_url.'/filemanager').'?directory=' .substr($image, strlen(DIR_IMAGE . 'catalog/')) . $url;
				}

				$this->data['folders'][] = array(
					'name'  => $name,
					'path'  => substr($image, strlen(DIR_IMAGE)),
					'href'  => $folder_href,
				);
			} elseif (is_file($image)) {
				$this->data['images'][] = array(
					'thumb' => $this->Model_tool_image->resize(substr($image, strlen(DIR_IMAGE)), 205, 200),
					'name'  => $name,
					'path'  => substr($image, strlen(DIR_IMAGE)),
					'href'  =>  base_url('/uploads/' . substr($image, strlen(DIR_IMAGE)))
				);
			}
		}

		$this->data['csrf_name'] = $this->security->get_csrf_token_name();
		$this->data['csrf_hash'] = $this->security->get_csrf_hash();

		$directory = $this->input->get('directory');
		if (isset($directory)) {
			$this->data['directory'] = urlencode($directory);
		} else {
			$this->data['directory'] = '';
		}

		$filter_name = $this->input->get('filter_name');
		if (isset($filter_name)) {
			$this->data['filter_name'] = $filter_name;
		} else {
			$this->data['filter_name'] = '';
		}

		// Return the target ID for the file manager to set the value
		$target = $this->input->get('target');
		if (isset($target)) {
			$this->data['target'] = $target;
		} else {
			$this->data['target'] = '';
		}

		// Return the thumbnail for the file manager to show a thumbnail
		$thumb = $this->input->get('thumb');
		if (isset($thumb)) {
			$this->data['thumb'] = $thumb;
		} else {
			$this->data['thumb'] = '';
		}

		// Parent
		$url = '';

		$directory = $this->input->get('directory');
		if (isset($directory)) {
			$pos = strrpos($directory, '/');

			if ($pos) {
				$url .= '&directory=' . urlencode(substr($directory, 0, $pos));
			}
		}

		$target = $this->input->get('target');
		if (isset($target)) {
			$url .= '&target=' . $target;
		}

		$thumb = $this->input->get('thumb');
		if (isset($thumb)) {
			$url .= '&thumb=' . $thumb;
		}

		if ($type == 'popup') {
			$this->data['parent'] = site_url_multi($this->admin_url.'/filemanager/index/popup').'?token=token'. $url;
		} elseif ($type == 'ckeditor') {
			$this->data['parent'] = site_url_multi($this->admin_url.'/filemanager/index/ckeditor').'?token=token'. $url;
		} else {
			$this->data['parent'] = site_url_multi($this->admin_url.'/filemanager').'?token=token'. $url;
		}
		// Refresh
		$url = '';

		$directory = $this->input->get('directory');
		if (isset($directory)) {
			$url .= '&directory=' . urlencode($directory);
		}

		$target = $this->input->get('target');
		if (isset($target)) {
			$url .= '&target=' . $target;
		}

		$thumb = $this->input->get('thumb');
		if (isset($thumb)) {
			$url .= '&thumb=' . $thumb;
		}

		if ($type == 'popup') {
			$this->data['refresh'] = site_url_multi($this->admin_url.'/filemanager/index/popup').'?token'.$url;
		} elseif ($type == 'ckeditor') {
			$this->data['refresh'] = site_url_multi($this->admin_url.'/filemanager/index/ckeditor').'?token'.$url;
		} else {
			$this->data['refresh'] = site_url_multi($this->admin_url.'/filemanager').'?token'.$url;
		}


		$url = '';

		$directory = $this->input->get('directory');
		if (isset($directory)) {
			$url .= '&directory=' . urlencode(html_entity_decode($directory, ENT_QUOTES, 'UTF-8'));
		}

		$filter_name = $this->input->get('filter_name');
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($filter_name, ENT_QUOTES, 'UTF-8'));
		}
		$target = $this->input->get('target');
		if (isset($target)) {
			$url .= '&target=' . $target;
		}
		$thumb = $this->input->get('thumb');
		if (isset($thumb)) {
			$url .= '&thumb=' . $thumb;
		}

		if ($type == 'popup') {
			$config['base_url'] = site_url_multi($this->admin_url.'/filemanager/index/popup');
		} elseif ($type == 'ckeditor') {
			$config['base_url'] = site_url_multi($this->admin_url.'/filemanager/index/ckeditor');
		} else {
			$config['base_url'] = site_url_multi($this->admin_url.'/filemanager');
		}

		$config['total_rows'] = $image_total;
		$config['per_page'] = 16;
		$config['page'] = $page;
		$config['url'] = $url;
		$this->data['pagination'] = $this->pagination($config);

		if ($type == 'popup') {
			$this->template->view($this->admin_url.'/'.$this->theme.'/filemanager/popup.tpl', $this->data);
		} elseif ($type == 'ckeditor') {
			$this->template->render('ckeditor', 'ckeditor');
		} else {
			$this->template->render();
		}
	}
	
	public function cropper()
	{
		$this->data['title'] = translate('cropper');
		$this->template->render();
	}

	public function uploadOLD()
	{
		$json = [];

		$directory = $this->input->get('directory');
		if (isset($directory)) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . $directory, '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		$config = array();
		$config['upload_path'] = $directory;
		$config['allowed_types'] = 'gif|jpg|png|pdf|doc|xls|xlsx|docx';
		//$config['max_size']      = '0';
		$config['overwrite']     = false;

		$this->load->library('upload');

		$files = $_FILES;
		$total = count($files['file']['name']);
		unset($_FILES);

		for ($i=0; $i< $total; $i++) {
			$_FILES['file']['name']= $files['file']['name'][$i];
			$_FILES['file']['type']= $files['file']['type'][$i];
			$_FILES['file']['tmp_name']= $files['file']['tmp_name'][$i];
			$_FILES['file']['error']= $files['file']['error'][$i];
			$_FILES['file']['size']= $files['file']['size'][$i];

			$this->upload->initialize($config);
			if (! $this->upload->do_upload('file')) {
				$json['error'] = $this->upload->display_errors();
			}
		}
		if (empty($json['error'])) {
			$json['success'] = 'Successfull uploaded';
		}
		
		$this->template->json($json);
	}

    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

	public function upload()
	{

        $dir = $this->input->get('directory');

        if($this->auth->is_member('vendor')) {
            $dir = $this->input->get('directory') . $this->slugify($this->auth->get_user()->brand . '-' . $this->auth->get_user()->id);

        }
        
        if (isset($dir))
		{
			$directory = rtrim(DIR_IMAGE.'catalog/'.$dir.'/');
		}
		else
		{
			$directory = DIR_IMAGE.'catalog';
		}

		if( ! is_dir($directory) ) {
		    mkdir($directory);
        }

		$config = [];
		$config['upload_path'] = $directory;
		$config['allowed_types'] = 'gif|jpg|png|GIF|jpeg|JPG|JPEG|PNG';
		$config['overwrite']     = false;

		$this->load->library('upload');

		if(isset($_FILES) && !empty($_FILES))
		{
			$files = $_FILES;
			$total = count($files['file']['name']);
			unset($_FILES);

			for ($i=0; $i< $total; $i++)
			{
				$_FILES['file']['name'] 		= $files['file']['name'][$i];
				$_FILES['file']['type'] 		= $files['file']['type'][$i];
				$_FILES['file']['tmp_name'] 	= $files['file']['tmp_name'][$i];
				$_FILES['file']['error'] 		= $files['file']['error'][$i];
				$_FILES['file']['size'] 		= $files['file']['size'][$i];

				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file'))
				{
					$this->data['response'] = [
						'success' => false,
						'message' => $this->upload->display_errors()
					];
				}
				else
				{
					$upload_data = $this->upload->data();
	
					$this->compressImage($upload_data);
					
					$this->data['response'] = [
						'success' 	=> true,
						'data'		=> $upload_data,
						'image'		=> $this->Model_tool_image->resize('catalog/'.$dir. '/' .$upload_data['file_name'], 250, 250),
						'save'		=> 'catalog/' . $dir . '/' . $upload_data['file_name'],
						'message' 	=> translate('successfully_uploaded')
					];
				}
			}

			$this->template->json($this->data['response']);
		}
	}

	public function compressImage($data) {

		if($data['image_type'] == 'gif'){ 
			$img = imagecreatefromgif($data['full_path']);
			imagealphablending($img, false);
			imagesavealpha($img, true);
			unlink($data['full_path']);
			imagegif($img, $data['full_path']);
		}
		elseif($data['image_type'] == 'png'){
			$img = imagecreatefrompng($data['full_path']);
			imagealphablending($img, false);
			imagesavealpha($img, true);
			unlink($data['full_path']);
			imagepng($img, $data['full_path'], 9);
		}
		else{ 
			$img = imagecreatefromjpeg($data['full_path']);
			unlink($data['full_path']);
			imagejpeg($img, $data['full_path'], 60);
		}
		
		imagedestroy($img);
	}
	
	
	public function folder()
	{
		$json = [];

		$directory = $this->input->get('directory');
		if (isset($directory)) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . $directory, '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}


		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			// Sanitize the folder name
			$folder = basename(html_entity_decode($this->input->post('folder'), ENT_QUOTES, 'UTF-8'));

			$json['folder'] = $folder;
			// Validate the filename length
			if ((strlen($folder) < 3) || (strlen($folder) > 128)) {
				$json['error'] = 'error_folder';
			}

			// Check if directory already exists or not
			if (is_dir($directory . '/' . $folder)) {
				$json['error'] = $directory . '/' . $folder;
			}
		}

		if (!isset($json['error'])) {
			mkdir($directory . '/' . $folder, 0777);
			chmod($directory . '/' . $folder, 0777);

			@touch($directory . '/' . $folder . '/' . 'index.html');

			$json['success'] = 'Direcory created';
		}

		$this->template->json($json);
	}

	public function delete()
	{
		$json = [];

		$path = $this->input->post('path');
		if (isset($path)) {
			$paths = $path;
		} else {
			$paths = [];
		}

		if (!$json) {
			// Loop through each path
			foreach ($paths as $path) {
				$path = rtrim(DIR_IMAGE . $path, '/');

				// If path is just a file delete it
				if (is_file($path)) {
					unlink($path);

				// If path is a directory beging deleting each file and sub folder
				} elseif (is_dir($path)) {
					$files = array();

					// Make path into an array
					$path = array($path . '*');

					// While the path array is still populated keep looping through
					while (count($path) != 0) {
						$next = array_shift($path);

						foreach (glob($next) as $file) {
							// If directory add to path array
							if (is_dir($file)) {
								$path[] = $file . '/*';
							}

							// Add the file to the files to be deleted array
							$files[] = $file;
						}
					}

					// Reverse sort the file array
					rsort($files);

					foreach ($files as $file) {
						// If file just delete
						if (is_file($file)) {
							unlink($file);

						// If directory use the remove directory function
						} elseif (is_dir($file)) {
							rmdir($file);
						}
					}
				}
			}

			$json['success'] = 'text_delete';
		}

		$this->template->json($json);
	}
	public function pagination($data)
	{
		$base_url = $data['base_url'];
		$total = $data['total_rows'];
		$per_page = $data['per_page'];
		$page = $data['page'];
		$url = $data['url'];
		$pages = intval($total/$per_page);
		if ($total%$per_page != 0) {
			$pages++;
		}
		$p = "";

		for ($i=1; $i<= $pages;$i++) {
			$p .= '<a class="btn directory" href="'.$base_url.'?page='.$i.$url.'" >'.$i.'</a>';
		}

		return $p;
	}
}
