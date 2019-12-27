<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Contact extends Site_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->data['title'] = translate('title');
        $this->template->render('contact');
    }
}