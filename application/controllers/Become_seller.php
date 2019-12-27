<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Become_seller extends Site_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->load->model('modules/Zone_model');
		$countries = $this->Country_model->filter(['status' => 1, 'sell' => 1])->with_translation()->order_by('name', 'ASC')->all();
		$this->data['countries'] = [];
		foreach($countries as $country)
		{
			$region_count = $this->Zone_model->filter(['country_id' => $country->id, 'sell' => 1])->count_rows();
			if($region_count)
			{
				$country_object = new stdClass();
				$country_object->id = $country->id;
				$country_object->name = $country->name;

				$this->data['countries'][] = $country_object;
			}
		}

	}

	public function index()
	{

	    if(get_country_id() != 221) {
            $this->load->library('shipping/Ems');
            $response = $this->ems->calculate();
        }


        foreach ($this->data['languages'] as $key => $value) {

            $link = site_url($key . '/become_seller');
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
		//var_dump($response);

		if($this->auth->is_loggedin())
		{
			redirect('administrator');
		}
		$this->data['title'] = translate('title');


		if($this->session->has_userdata('success'))
		{
			$this->data['success'] = $this->session->userdata('success');
		}

		$this->form_validation->set_rules('firstname', translate('firstname'), 'required|trim');
		$this->form_validation->set_rules('lastname', translate('lastname'), 'required|trim');
		$this->form_validation->set_rules('password', translate('password'), 'required|trim');
		$this->form_validation->set_rules('password_confirm', translate('password_confirm'), 'required|trim|matches[password]');
		//$this->form_validation->set_rules('date_of_birth', translate('date_of_birth'), 'required|trim');
		$this->form_validation->set_rules('email', translate('email'), 'required|trim');
		$this->form_validation->set_rules('mobile', translate('mobile'), 'required|trim');
		$this->form_validation->set_rules('country_id', translate('country'), 'required|trim');
		$this->form_validation->set_rules('zone_id', translate('city'), 'required|trim');
		$this->form_validation->set_rules('address', translate('address'), 'required|trim');
		$this->form_validation->set_rules('postcode', translate('postcode'), 'required|trim');
		$this->form_validation->set_rules('brand', translate('brand'), 'required|trim');
		$this->form_validation->set_rules('type', translate('type'), 'required|trim');


		if($this->form_validation->run())
		{

			$img = $this->input->post('front_side', false);
			$img = str_replace('data:image/png;base64,', '', $img);
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace(' ', '+', $img);

			$vendor_data = [
				'firstname'					=> $this->input->post('firstname'),
				'lastname'					=> $this->input->post('lastname'),
				'pass'						=> $this->auth->hash_password($this->input->post('password'), 0),
				'email'						=> $this->input->post('email'),
				'mobile'					=> $this->input->post('mobile'),
				'country'					=> $this->input->post('country_id'),
				'city'						=> $this->input->post('zone_id'),
				'address'					=> $this->input->post('address'),
				'address2'					=> $this->input->post('address2'),
				'postcode'					=> $this->input->post('postcode'),
				'file'						=> implode(',', $_POST['files']),
				'brand'						=> $this->input->post('brand'),
				'type'						=> $this->input->post('type'),
				'banned'					=> 1
			];


			$user_id = $this->User_model->insert($vendor_data);

			if($user_id){
				$this->db->insert('user_to_group', ['user_id' => $user_id, 'group_id' => 2]);

				$this->email->from($this->config->item('auth')['email'], $this->config->item('auth')['name']);
				$this->email->to($this->input->post('email'));

				$this->email->subject(translate('thanks_register_title'));
				$this->email->message(sprintf(translate('thanks_register'), $this->input->post('email'), $this->input->post('password')));

				$this->email->send();
			}

			$this->session->set_flashdata('success', translate('success'));

			redirect(site_url_multi('become_seller'));

		}
		else
		{
			$this->data['message'] = validation_errors();
		}

        if($success = $this->session->flashdata('success')) {
            $this->data['success'] = $success;
        }

		$this->template->render('become_seller');
	}

	public function upload()
	{
		$directory = DIR_IMAGE . 'catalog/document';
		$upload_dir = 'catalog/document/';

		$config = [];
		$config['upload_path'] = $directory;
		$config['allowed_types'] = 'gif|jpg|png|jpeg|doc|docx|pdf|xls|xlsx|txt|rtf';
		$config['overwrite']     = false;

		$this->load->library('upload');

		$this->upload->initialize($config);
		$data = $this->upload->do_upload('file');
		if (! $data) {
			$this->data['response'] = [
				'success' => false,
				'message' => $this->upload->display_errors()
			];
		}


		if (empty($json['error']))
		{
			$upload_data = $this->upload->data();

			$this->data['response'] = [
				'success' 	=> true,
				'image' 	=> $upload_dir.$upload_data['file_name'],
 				'message'	=> translate('successfully_upload', true)
			];
		}

		$this->template->json($this->data['response']);
	}

	public function forget_password()
    {

		$this->data['title'] = translate('form_label_forgot_password');

        $this->form_validation->set_rules('email', translate('email'), 'required|trim');

        if ($this->form_validation->run()) {
            if ($this->auth->remind_password($this->input->post('email'))) {
                $this->data['message'] = translate('successfully_forget_password');
            } else {
                $this->data['message'] = translate('user_not_found');
            }
        } else {
            $this->data['message'] = validation_errors();
        }

        $this->template->render('seller_forget');
    }

	public function reset_password($key = false)
    {
		if($this->input->server('REQUEST_METHOD') != 'POST' && !$this->auth->getByKey($key))
			redirect(site_url('become_seller/login'));

        $this->data['title'] = translate('form_label_forgot_password');

		$this->form_validation->set_rules('password', translate('form_label_password'), 'required|trim');

        if ($this->form_validation->run()) {
			$this->auth->reset_password($this->input->post('key'), $this->input->post('password'));
			print '<script>alert("'.translate('form_success_password').'");location.href="'.site_url('become_seller/login').'"</script>';
		}

		$this->data['key'] = $key;
		$this->template->render('seller_reset_password');
    }

	public function login()
	{
		if($this->auth->is_loggedin())
		{
			redirect(site_url_multi('administrator'));
		}

        foreach ($this->data['languages'] as $key => $value) {

            $link = site_url($key . '/become_seller/login');
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

		$this->data['title'] = translate('title');

		$this->form_validation->set_rules('login', 'login', 'required|trim');
		$this->form_validation->set_rules('password', 'password', 'required|trim');

		if ($this->form_validation->run() === true)
		{

			if ($this->auth->login($this->input->post('login'), $this->input->post('password')))
			{

				redirect(site_url_multi('administrator/dashboard'), 'refresh');
			}
			else
			{
				$this->session->set_flashdata('message', $this->auth->print_errors());
				redirect(site_url_multi('become_seller/login'), 'refresh');
			}
		} else {
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->template->render('seller_login');
		}
	}

	public function view()
	{
		$this->data['title'] = translate('title');
		$this->template->render('become_seller_view');
	}
}
