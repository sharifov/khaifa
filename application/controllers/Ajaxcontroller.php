<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ajaxcontroller extends CI_Controller
{
	public function __construct() {
        parent::__construct();
    }

    public function get_users() {
        echo json_encode([
			[
                'id'    => '1',
				'name'  => 'Farhad',
				'surname' => 'Misirli'
			],
			[
                'id'    => '3',
				'name' => 'Nicat',
				'surname' => 'Mahirson'
			],
			[
                'id'    => '2',
				'name' => 'Vugar',
				'surname' => 'Ahmedoglu'
			],
			[
                'id'    => '4',
				'name' => 'Kamran',
				'surname' => 'Nadjafzadeh'
			],
			[
                'id'    => '5',
				'name' => 'Elvin',
				'surname' => 'Mammadov'
			],
			[
                'id'    => '8',
				'name' => 'Dergax',
				'surname' => 'Abdullayev'
			]
		]);
    }

    public function get_user_detail($id = false) {
        $response = [];
        if($id == 1) {
            $response = [
                'id'        => 1,
                'name'      => 'Farhad',
                'surname'   => 'Misirli',
                'email'     => "farhadmisirli@gmail.com",
                'image'     => 'https://scontent.fgyd4-2.fna.fbcdn.net/v/t1.0-9/49013282_2296407193914482_2367924914641436672_n.jpg?_nc_cat=102&_nc_ht=scontent.fgyd4-2.fna&oh=e3cc40e57ea31e8b8340d0137e499f9c&oe=5D41D735',
                'about'     => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries",
                'number'    => '+994503976794'
            ];
        } elseif($id == 2) {
            $response = [
                'id'        => 2,
                'name'      => 'Vugar',
                'surname'   => 'Dadalov',
                'email'     => "vugardadalov@gmail.com",
                //'image'     => 'https://scontent.fgyd4-2.fna.fbcdn.net/v/t1.0-9/36481205_1613366558849924_1664983637533130752_n.jpg?_nc_cat=102&_nc_ht=scontent.fgyd4-2.fna&oh=f88dd9d2531bec735a59be18460cd530&oe=5D47AD91',
                'image'     => '',
                'about'     => "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English",
                'number'    => '+994708317635'
            ];
        } elseif($id == 3) {
            $response = [
                'id'        => 3,
                'name'      => 'Nicat',
                'surname'   => 'Mahirson',
                'email'     => "nicat@mahirson.com",
                'image'     => 'https://scontent.fgyd4-2.fna.fbcdn.net/v/t1.0-9/10399829_1040762342633785_6011533984938882174_n.jpg?_nc_cat=102&_nc_ht=scontent.fgyd4-2.fna&oh=a24fb1284f80cc1215f32deed14952bf&oe=5D0EC707',
                'about'     => "There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable",
                'number'    => '+994503395949'
            ];
        }

        echo json_encode([$response]);
    }

    public function get_message() {
        echo json_encode("Hello world");
    }

    public function save_user() {
        $response = ['status' => false, 'message' => ''];
        $this->form_validation->set_rules('firstname','Firstname','trim|required');
        $this->form_validation->set_rules('lastname','Lastname','trim|required');
        $this->form_validation->set_rules('email','Email','trim|required|valid_email');
        $this->form_validation->set_rules('password','Password','trim|required|min_length[5]');

        if ($this->input->method() == 'post') {
            if($this->form_validation->run() == true) {
                $data = [
                    'firstname' => $this->input->post('firstname'),
                    'lastname' => $this->input->post('lastname'),
                    'email' => $this->input->post('email'),
                    'password' => $this->input->post('password')
                ];
                $this->db->insert('ajax_user', $data);
                $response['status'] = true;
            } else{
                $response['message'] = strip_tags(validation_errors());
            }
        }
        
        echo json_encode($response);
    }

    public function login() {
        $response = ['status' => false, 'message' => ''];
        $this->form_validation->set_rules('email','Email','trim|required|valid_email');
        $this->form_validation->set_rules('password','Password','trim|required');

        if ($this->input->method() == 'post') {
            if($this->form_validation->run() == true) {
                $data = [
                    'email' => $this->input->post('email'),
                    'password' => $this->input->post('password')
                ];
                
                $this->db->where($data);
                $query = $this->db->get('ajax_user');
                if($query->num_rows() > 0) {
                    $response['status'] = true;
                    $response['user_id'] = $query->row()->id;
                } else {
                    $response['message'] = "Wrong email or password";
                }
                
            } else{
                $response['message'] = strip_tags(validation_errors());
            }
        }
        
        echo json_encode($response);
    }

    public function get_user() {
        $response = ['status' => false];
        $this->form_validation->set_rules('id','id','required|is_natural_no_zero');

        if ($this->input->method() == 'post') {
            if($this->form_validation->run() == true) {
                $user_id = $this->input->post('id');
                $this->db->where(['id' => $user_id]);
                $query = $this->db->get('ajax_user');
                if($query->num_rows() > 0) {
                    $response['status'] = true;
                    $user = $query->row();
                    $response['firstname'] = $user->firstname;
                    $response['lastname'] = $user->lastname;
                    $response['email'] = $user->email;
                    $response['phone'] = $user->phone;
                    $response['image'] = $user->image;
                    $response['location'] = $user->location;
                    $response['images'] = [
                        'https://scontent.fgyd4-1.fna.fbcdn.net/v/t1.0-9/49804359_2023381144371895_8013328523773083648_n.jpg?_nc_cat=105&_nc_ht=scontent.fgyd4-1.fna&oh=6cbb293a364b1f4cab51919b2080c4b9&oe=5D3E3697',
                        'https://scontent.fgyd4-2.fna.fbcdn.net/v/t1.0-9/49585766_284519635748466_9083951097709592576_n.jpg?_nc_cat=104&_nc_ht=scontent.fgyd4-2.fna&oh=6391009a9af5ea7abb0017b9712a0add&oe=5D0254CC',
                        'https://scontent.fgyd4-1.fna.fbcdn.net/v/t1.0-9/49121094_2293725034182698_7113135964448358400_n.jpg?_nc_cat=106&_nc_ht=scontent.fgyd4-1.fna&oh=188cdb53345a02b9933d8b4fa849c557&oe=5D127FBA'
                    ];
                } else {
                    $response['message'] = 'User not found!';
                }
            } else{
                $response['message'] = strip_tags(validation_errors());
            }
        }
        
        echo json_encode($response);
    } 

    


    
}
