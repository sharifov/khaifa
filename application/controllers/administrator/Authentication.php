<?php defined('BASEPATH') or exit('No direct script access allowed');

class Authentication extends Administrator_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * public function login()
	 * Sets rules of form validation for login page. Authenticates user with given details in POST method and redirects proper page.
	 * @see application/libraries/Auth.php
	 */
	public function login()
	{
		
		$this->data['title'] = translate('login_title');

		$this->form_validation->set_rules('login', 'login', 'required|trim');
		$this->form_validation->set_rules('password', 'password', 'required|trim');

		if ($this->form_validation->run() === true) {
			$this->auth->clear_errors();
			$remember = (bool)$this->input->post('remember');

			if ($this->auth->login($this->input->post('login'), $this->input->post('password'), $remember)) {
				redirect(site_url_multi($this->directory.'dashboard'), 'refresh');
			} else {
				$this->session->set_flashdata('message', $this->auth->print_errors());
				redirect(site_url_multi($this->directory.'authentication/login'), 'refresh');
			}
		} else {
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->data['copyright'] = sprintf(translate('footer_copyright', true), date('Y'), VERSION);
			$this->template->render();
		}
	}

	/**
	 * public function logout()
	 * Unsets user's cookie and redirects to the login page
	 *
	 * @see application/libraries/Auth.php
	 */
	public function logout()
	{   
		
		if($this->auth->is_member('vendor'))
		{	
			$this->auth->logout();
			redirect('become_seller/login', 'refresh');
		}
		else
		{
			$this->auth->logout();
			redirect($this->directory.'/authentication/login', 'refresh');
		}
		
	}
}
