<?php
defined('BASEPATH') or exit('No direct script access allowed');

class faq extends Site_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("modules/Faq_model");
        $this->load->model("modules/Faq_category_model");
    }

    public function index()
    {
        $this->data['title'] = translate('index_title');
        $this->data['faq_categories'] = $this->Faq_category_model->filter(['status' => 1])->with_translation()->all();
        $this->data['sidebar_active_menu'] = 'faq';
        $this->template->render('faq');
    }

    public function category($slug = false,$page = 1)
    {
        if($slug) {
            $slug = urldecode($slug);
            $this->data['faq_categories'] = $this->Faq_category_model->filter(['status' => 1])->order_by('sort','ASC')->with_translation()->all();
            if($this->data['faq_categories']) {
                foreach($this->data['faq_categories'] as $index => $faq_category) {
                    if(trim($slug) == trim($faq_category->slug)) {
                        $this->data['current_faq_category'] = $faq_category;
                    }
                }

                if($this->data['current_faq_category']) {
                    $this->data['title'] = $this->data['current_faq_category']->name;
                    $where = ['faq_category_id' => $this->data['current_faq_category']->id, 'status' => 1];
                    if($this->input->get('faq-query')) {
                        $where["name LIKE'%".$this->input->get("faq-query")."%'"] = null;
                    }
                    $this->data['faqs'] = $this->Faq_model->filter($where)->order_by('sort','ASC')->limit(1, $page - 1)->with_translation()->all();
                    $total_rows = $this->Faq_model->filter($where)->with_translation()->count_rows();

                    // Sets Pagination options and initialize
                    $config['base_url'] = site_url_multi('faq/category/').$slug;
                    $config['total_rows'] = $total_rows;
                    $config['per_page'] = 1;
                    $config['full_tag_open']            = '<div class="text-center"><div class="pagination main-pagination">';
                    $config['full_tag_close']           = '</div></div>';
                    $config['first_link'] 				= '&laquo;';
                    $config['first_tag_open'] 			= '';
                    $config['first_tag_close']			= '';
                    $config['last_link'] 				= '&raquo;';
                    $config['last_tag_open'] 			= '';
                    $config['last_tag_close'] 			= '';
                    $config['next_link'] 				= '&rarr;';
                    $config['next_tag_open'] 			= '';
                    $config['next_tag_close'] 			= '';
                    $config['prev_link'] 				= '&larr;';
                    $config['prev_tag_open'] 			= '';
                    $config['prev_tag_close'] 			= '';
                    $config['cur_tag_open'] 			= '<a class="active">';
                    $config['cur_tag_close'] 			= '</a>';
                    $config['num_tag_open'] 			= '';
                    $config['num_tag_close'] 			= '';

                    $this->pagination->initialize($config);
                    $this->data['pagination'] = $this->pagination->create_links();
                    
                }

            } else {
                show_404();
            }
            

        }

        $this->template->render('faq-list');
    }

    public function view($slug = false)
    {
        if($slug) {
            $slug = urldecode($slug);
            $faq = $this->Faq_model->filter(['slug' => $slug, 'status' => 1])->with_translation()->one();
            if($faq) {
                $this->data['faq_categories'] = $this->Faq_category_model->filter(['status' => 1])->order_by('sort','ASC')->with_translation()->all();
                if($this->data['faq_categories']) {
                    foreach($this->data['faq_categories'] as $index => $faq_category) {
                        if($faq->faq_category_id = $faq_category->id) {
                            $this->data['current_faq_category'] = $faq_category;
                        }
                    }
                    $this->data['faq'] = $faq;    
                    $this->data['title'] = $faq->name;
                    $this->template->render('faq-single');
                } else {
                    show_404();
                }

              
            } else {
                show_404();
            }
        } else {
            show_404();
        }
        
    }

    

}
