<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Customer
{
    public $CI;
    public $config_vars;
    public $errors = [];
    public $infos = [];
    public $flash_errors = [];
    public $flash_infos = [];
    
    public function __construct()
    {
        // get main CI object
        $this->CI = &get_instance();

        $this->CI->config->load('auth');
        $this->config_vars = $this->CI->config->item('auth');
    }

    public function login($identifier, $pass, $remember = false)
    {
        // Remove cookies first
        $cookie = [
            'name' => 'customer',
            'value' => '',
            'expire' => -3600,
            'path' => '/',
        ];
        $this->CI->input->set_cookie($cookie);

        // Check email exist
        $query = null;
        $query = $this->CI->db->where('email', $identifier);
        $query = $this->CI->db->get('customer');
        if($query->num_rows() == 0) {
            $this->error(translate('form_error_no_user'));
            return false;
        }

        $row = $query->row();
        // Check email and password
        if(!$this->verify_password($pass, $row->password)) {
            $this->error(translate('form_error_wrong_password'));
            return false;
        }
        

        // Check user status
        if($query->num_rows() == 0) {
            $this->error(translate('form_error_doenst_active'));
            return false;
        }

        // create session
        $data = [
            'customer_id'   => $row->id,
            'firstname'     => $row->firstname,
            'lastname'      => $row->lastname,
            'email'         => $row->email,
            'loggedin'      => true
        ];
        $this->CI->session->set_userdata($data);
        
        return true;
    }



    public function update_login_attempts()
    {
        $ip_address = $this->CI->input->ip_address();
        $query = $this->CI->db->where(
            [
                'ip_address' => $ip_address,
                'timestamp >=' => date("Y-m-d H:i:s",
                    strtotime("-" . $this->config_vars['max_login_attempt_time_period']))
            ]
        );
        $query = $this->CI->db->get($this->config_vars['login_attempts']);

        if ($query->num_rows() == 0) {
            $data = [];
            $data['ip_address'] = $ip_address;
            $data['timestamp'] = date("Y-m-d H:i:s");
            $data['login_attempts'] = 1;
            $this->CI->db->insert($this->config_vars['login_attempts'], $data);
            return true;
        } else {
            $row = $query->row();
            $data = [];
            $data['timestamp'] = date("Y-m-d H:i:s");
            $data['login_attempts'] = $row->login_attempts + 1;
            $this->CI->db->where('id', $row->id);
            $this->CI->db->update($this->config_vars['login_attempts'], $data);

            if ($data['login_attempts'] > $this->config_vars['max_login_attempt']) {
                return false;
            } else {
                return true;
            }
        }

    }

    /**
     * Error
     * Add message to error array and set flash data
     * @param string $message Message to add to array
     * @param boolean $flashdata if true add $message to CI flashdata (deflault: false)
     */
    public function error($message = '', $flashdata = false)
    {
        $this->errors[] = $message;
        if ($flashdata) {
            $this->flash_errors[] = $message;
            $this->CI->session->set_flashdata('errors', $this->flash_errors);
        }
    }

    //tested

    /**
     * Get login attempt
     * @return int
     */
    public function get_login_attempts()
    {
        $ip_address = $this->CI->input->ip_address();
        $query = $this->CI->db->where(
            [
                'ip_address' => $ip_address,
                'timestamp >=' => date("Y-m-d H:i:s",
                    strtotime("-" . $this->config_vars['max_login_attempt_time_period']))
            ]
        );
        $query = $this->CI->db->get($this->config_vars['login_attempts']);

        if ($query->num_rows() != 0) {
            $row = $query->row();
            return $row->login_attempts;
        }

        return 0;
    }

    //tested

    /**
     * Hash password
     * Hash the password for storage in the database
     * (thanks to Jacob Tomlinson for contribution)
     * @param string $pass Password to hash
     * @param $userid
     * @return string Hashed password
     */
    function hash_password($pass, $userid)
    {
        if ($this->config_vars['use_password_hash']) {
            return password_hash($pass, $this->config_vars['password_hash_algo'],
                $this->config_vars['password_hash_options']);
        } else {
            $salt = md5($userid);
            return hash($this->config_vars['hash'], $salt . $pass);
        }
    }

    /**
     * Verify password
     * Verfies the hashed password
     * @param string $password Password
     * @param string $hash Hashed Password
     * @param string $user_id
     * @return bool False or True
     */
    function verify_password($password, $hash)
    {
        if (true) {
            return password_verify($password, $hash);
        } else {
            return ($password == $hash ? true : false);
        }
    }

    /**
     * Update remember
     * Update amount of time a user is remembered for
     * @param int $user_id User id to update
     * @param int $expression
     * @param int $expire
     * @return bool Update fails/succeeds
     */
    public function update_remember($user_id, $expression = null, $expire = null)
    {

        $data['remember_time'] = $expire;
        $data['remember_exp'] = $expression;

        $query = $this->CI->db->where('id', $user_id);
        return $this->CI->db->update('customer', $data);
    }

    /**
     * Update last login
     * Update user's last login date
     * @param int|bool $user_id User id to update or false for current user
     * @return bool Update fails/succeeds
     */
    public function update_last_login($customer_id = false)
    {

        if ($customer_id == false) {
            $customer_id = $this->CI->session->userdata('customer_id');
        }

        $data['last_login'] = date("Y-m-d H:i:s");
        $data['ip_address'] = $this->CI->input->ip_address();

        $this->CI->db->where('id', $customer_id);
        return $this->CI->db->update('customers', $data);
    }

    //tested

    /**
     * Update activity
     * Update user's last activity date
     * @param int|bool $user_id User id to update or false for current user
     * @return bool Update fails/succeeds
     */
    public function update_activity($user_id = false)
    {

        if ($user_id == false) {
            $user_id = $this->CI->session->userdata('id');
        }

        if ($user_id == false) {
            return false;
        }

        $data['last_activity'] = date("Y-m-d H:i:s");

        $query = $this->CI->db->where('id', $user_id);
        return $this->CI->db->update('customer', $data);
    }


    //tested

    /**
     * Reset last login attempts
     * Removes a Login Attempt
     * @return bool Reset fails/succeeds
     */
    public function reset_login_attempts()
    {
        $ip_address = $this->CI->input->ip_address();
        $this->CI->db->where(
            [
                'ip_address' => $ip_address,
                'timestamp >=' => date("Y-m-d H:i:s",
                    strtotime("-" . $this->config_vars['max_login_attempt_time_period']))
            ]
        );
        return $this->CI->db->delete($this->config_vars['login_attempts']);
    }

    /**
     * Controls if a logged or public user has permission
     *
     * If user does not have permission to access page, it stops script and gives
     * error message, unless 'no_permission' value is set in config.  If 'no_permission' is
     * set in config it redirects user to the set url and passes the 'no_access' error message.
     * It also updates last activity every time function called.
     *
     * @param bool $perm_par If not given just control user logged in or not
     */
    public function control($perm_par = false)
    {

        if ($this->CI->session->userdata('totp_required')) {
            $this->error($this->CI->lang->line('authentication_error_totp_verification_required'));
            redirect($this->config_vars['totp_two_step_login_redirect']);
        }

        $perm_id = $this->get_perm_id($perm_par);
        $this->update_activity();
        if ($perm_par == false) {
            if ($this->is_loggedin()) {
                return true;
            } else {
                if (!$this->is_loggedin()) {
                    $this->error($this->CI->lang->line('authentication_error_no_access'));
                    if ($this->config_vars['no_permission'] !== false) {
                        redirect($this->config_vars['no_permission']);
                    }
                }
            }

        } else {
            if (!$this->is_allowed($perm_id) OR !$this->is_group_allowed($perm_id)) {
                if ($this->config_vars['no_permission']) {
                    $this->error($this->CI->lang->line('authentication_error_no_access'));
                    if ($this->config_vars['no_permission'] !== false) {
                        redirect($this->config_vars['no_permission']);
                    }
                } else {
                    echo $this->CI->lang->line('authentication_error_no_access');
                    die();
                }
            }
        }
    }

    /**
     * Get permission id
     * Get permission id from permisison name or id
     * @param int|string $perm_par Permission id or name to get
     * @return int Permission id or null if perm does not exist
     */
    public function get_perm_id($perm_par)
    {
        if (is_numeric($perm_par)) {
            return $perm_par;
        }

        $query = $this->CI->db->where('name', $perm_par);
        $query = $this->CI->db->get($this->config_vars['perms']);

        if ($query->num_rows() == 0) {
            return false;
        }

        $row = $query->row();
        return $row->id;
    }


    ########################
    # User Functions
    ########################

    //tested

    /**
     * Check user login
     * Checks if user logged in, also checks remember.
     * @return bool
     */
    public function is_loggedin()
    {
        if ($this->CI->session->userdata('customer_id')) {
            return true;
        } else {
            if (!$this->CI->input->cookie('customer', true)) {
                return false;
            }
        }
        return false;
    }

    //tested

    /**
     * Fast login
     * Login with just a user id
     * @param int $user_id User id to log in
     * @return bool true if login successful.
     */
    public function login_fast($user_id)
    {

        $query = $this->CI->db->where('id', $user_id);
        $query = $this->CI->db->get('customer');

        $row = $query->row();

        if ($query->num_rows() > 0) {

            // if id matches
            // create session
            $data = [
                'customer_id'   => $row->id,
                'firstname'     => $row->firstname,
                'lastname'      => $row->lastname,
                'email'         => $row->email,
                'loggedin'      => true
            ];
            $this->CI->session->set_userdata($data);
            return true;
        }
        return false;
    }

    //tested

    /**
     * Is user allowed
     * Check if user allowed to do specified action, admin always allowed
     * first checks user permissions then check group permissions
     * @param int $perm_par Permission id or name to check
     * @param int|bool $user_id User id to check, or if false checks current user
     * @return bool
     */
    public function is_allowed($perm_par, $user_id = false)
    {

        if ($this->CI->session->userdata('totp_required')) {
            $this->error($this->CI->lang->line('authentication_error_totp_verification_required'));
            redirect($this->config_vars['totp_two_step_login_redirect']);
        }

        if ($user_id == false) {
            $user_id = $this->CI->session->userdata('id');
        }

        if ($this->is_admin($user_id)) {
            return true;
        }

        $perm_id = $this->get_perm_id($perm_par);

        $query = $this->CI->db->where('perm_id', $perm_id);
        $query = $this->CI->db->where('user_id', $user_id);
        $query = $this->CI->db->get($this->config_vars['perm_to_user']);

        if ($query->num_rows() > 0) {
            return true;
        } else {
            $g_allowed = false;
            foreach ($this->get_user_groups($user_id) as $group) {
                if ($this->is_group_allowed($perm_id, $group->id)) {
                    $g_allowed = true;
                    break;
                }
            }
            return $g_allowed;
        }
    }

    //tested

    /**
     * Is admin
     * Check if current user is a member of the admin group
     * @param int $user_id User id to check, if it is not given checks current user
     * @return bool
     */
    public function is_admin($user_id = false)
    {

        return $this->is_member($this->config_vars['admin_group'], $user_id);
    }

    /**
     * Is member
     * Check if current user is a member of a group
     * @param int|string $group_par Group id or name to check
     * @param int|bool $user_id User id, if not given current user
     * @return bool
     */
    public function is_member($group_par, $user_id = false)
    {

        // if user_id false (not given), current user
        if (!$user_id) {
            $user_id = $this->CI->session->userdata('id');
        }

        $group_id = $this->get_group_id($group_par);

        $query = $this->CI->db->where('user_id', $user_id);
        $query = $this->CI->db->where('group_id', $group_id);
        $query = $this->CI->db->get($this->config_vars['user_to_group']);

        $row = $query->row();

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get group id
     * Get group id from group name or id ( ! Case sensitive)
     * @param int|string $group_par Group id or name to get
     * @return int Group id
     */
    public function get_group_id($group_par)
    {

        if (is_numeric($group_par)) {
            return $group_par;
        }

        $query = $this->CI->db->where('name', $group_par);
        $query = $this->CI->db->get($this->config_vars['groups']);

        if ($query->num_rows() == 0) {
            return false;
        }

        $row = $query->row();
        return $row->id;
    }

    //not tested excatly

    /**
     * Get user groups
     * Get groups a user is in
     * @param int|bool $user_id User id to get or false for current user
     * @return array Groups
     */
    public function get_user_groups($user_id = false)
    {

        if (!$user_id) {
            $user_id = $this->CI->session->userdata('id');
        }
        if (!$user_id) {
            $this->CI->db->where('name', $this->config_vars['public_group']);
            $query = $this->CI->db->get($this->config_vars['groups']);
        } else {
            if ($user_id) {
                $this->CI->db->join($this->config_vars['groups'], "id = group_id");
                $this->CI->db->where('user_id', $user_id);
                $query = $this->CI->db->get($this->config_vars['user_to_group']);
            }
        }
        return $query->result();
    }

    //tested

    /**
     * Is Group allowed
     * Check if group is allowed to do specified action, admin always allowed
     * @param int $perm_par Permission id or name to check
     * @param int|string|bool $group_par Group id or name to check, or if false checks all user groups
     * @return bool
     */
    public function is_group_allowed($perm_par, $group_par = false)
    {

        $perm_id = $this->get_perm_id($perm_par);

        // if group par is given
        if ($group_par != false) {

            // if group is admin group, as admin group has access to all permissions
            if (strcasecmp($group_par, $this->config_vars['admin_group']) == 0) {
                return true;
            }

            $subgroup_ids = $this->get_subgroups($group_par);
            $group_par = $this->get_group_id($group_par);
            $query = $this->CI->db->where('perm_id', $perm_id);
            $query = $this->CI->db->where('group_id', $group_par);
            $query = $this->CI->db->get($this->config_vars['perm_to_group']);

            $g_allowed = false;
            if (is_array($subgroup_ids)) {
                foreach ($subgroup_ids as $g) {
                    if ($this->is_group_allowed($perm_id, $g->subgroup_id)) {
                        $g_allowed = true;
                    }
                }
            }

            if ($query->num_rows() > 0) {
                $g_allowed = true;
            }
            return $g_allowed;
        }
        // if group par is not given
        // checks current user's all groups
        else {
            // if public is allowed or he is admin
            if ($this->is_admin($this->CI->session->userdata('id')) OR
                $this->is_group_allowed($perm_id, $this->config_vars['public_group'])) {
                return true;
            }

            // if is not login
            if (!$this->is_loggedin()) {
                return false;
            }

            $group_pars = $this->get_user_groups();
            foreach ($group_pars as $g) {
                if ($this->is_group_allowed($perm_id, $g->id)) {
                    return true;
                }
            }
            return false;
        }
    }

    //tested

    /**
     * Get subgroups
     * Get subgroups from group name or id ( ! Case sensitive)
     * @param int|string $group_par Group id or name to get
     * @return object Array of subgroup_id's
     */
    public function get_subgroups($group_par)
    {

        $group_id = $this->get_group_id($group_par);

        $query = $this->CI->db->where('group_id', $group_id);
        $query = $this->CI->db->select('subgroup_id');
        $query = $this->CI->db->get($this->config_vars['group_to_group']);

        if ($query->num_rows() == 0) {
            return false;
        }

        return $query->result();
    }

    //tested

    /**
     * Logout user
     * Destroys the CodeIgniter session and remove cookies to log out user.
     * @return bool If session destroy successful
     */
    public function logout()
    {

        $cookie = [
            'name' => 'customer',
            'value' => '',
            'expire' => -3600,
            'path' => '/',
        ];

        $this->CI->input->set_cookie($cookie);

        return $this->CI->session->sess_destroy();
    }

    /**
     * Remind password
     * Emails user with link to reset password
     * @param string $email Email for account to remind
     * @return bool Remind fails/succeeds
     */
    public function remind_password($email)
    {

        $query = $this->CI->db->where('email', $email);
        $query = $this->CI->db->get('customer');

        if ($query->num_rows() > 0) {
            $row = $query->row();

            $ver_code = sha1(strtotime("now"));

            $data['verification_code'] = $ver_code;

            $this->CI->db->where('email', $email);
            $this->CI->db->update('customer', $data);

            if (isset($this->config_vars['email_config']) && is_array($this->config_vars['email_config'])) {
                $this->CI->email->initialize($this->config_vars['email_config']);
            }

            $this->CI->email->from($this->config_vars['email'], $this->config_vars['name']);
            $this->CI->email->to($row->email);
            $this->CI->email->subject($this->CI->lang->line('authentication_email_reset_subject'));
			$_l = site_url() . $this->config_vars['reset_password_link'] . $ver_code;
            $this->CI->email->message($this->CI->lang->line('authentication_email_reset_text') . '<a href="'.$_l.'">'.$_l.'</a>');
            $this->CI->email->set_newline("\r\n");  

            $send = $this->CI->email->send();
            return true;
        }
        return false;
    }
	
	public function getByKey($ver=false){
		$query = $this->CI->db->where('verification_code', $ver);
        $query = $this->CI->db->get('customer');
		return $query->row();
	}
	

    /**
     * Reset password
     * Generate new password and email it to the user
     * @param string $ver_code Verification code for account
     * @return bool Password reset fails/succeeds
     */
    public function reset_password($ver_code, $pass = false)
    {
		
        $query = $this->CI->db->where('verification_code', $ver_code);
        $query = $this->CI->db->get('customer');

		if(!$pass){
			$pass_length = ($this->config_vars['min'] & 1 ? $this->config_vars['min'] + 1 : $this->config_vars['min']);
			$pass = random_string('alnum', $pass_length);
		}
		
        if ($query->num_rows() > 0) {

            $row = $query->row();

            $data = [
                'verification_code' => '',
                'password' => $this->hash_password($pass, $row->id)
            ];

            if ($this->config_vars['totp_active'] == true AND $this->config_vars['totp_reset_over_reset_password'] == true) {
                $data['totp_secret'] = null;
            }

            $email = $row->email;

            $this->CI->db->where('id', $row->id);
            $this->CI->db->update('customer', $data);

            if (isset($this->config_vars['email_config']) && is_array($this->config_vars['email_config'])) {
                $this->CI->email->initialize($this->config_vars['email_config']);
            }

            $this->CI->email->from($this->config_vars['email'], $this->config_vars['name']);
            $this->CI->email->to($email);
            $this->CI->email->subject($this->CI->lang->line('authentication_email_reset_subject'));
            $this->CI->email->message($this->CI->lang->line('authentication_email_change_success_new_password') . $pass);
            $this->CI->email->send();

            return true;
        }

        $this->error($this->CI->lang->line('authentication_error_vercode_invalid'));
        return false;
    }

    /**
     * Create user
     * Creates a new user
     * @param string $email User's email address
     * @param string $pass User's password
     * @param string $username User's username
     * @return int|bool False if create fails or returns user id if successful
     */
    public function create_user($email, $pass, $username = false, $firstname = '', $lastname = '', $group_id = false)
    {

        $valid = true;

        if ($this->config_vars['login_with_name'] == true) {
            if (empty($username)) {
                $this->error($this->CI->lang->line('authentication_error_username_required'));
                $valid = false;
            }
        
            if ($this->user_exist_by_username($username) && $username != false) {
                $this->error($this->CI->lang->line('authentication_error_username_exists'));
                $valid = false;
            }
        }

        if ($this->user_exist_by_email($email)) {
            $this->error($this->CI->lang->line('authentication_error_email_exists'));
            $valid = false;
        }
        $valid_email = (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$valid_email) {
            $this->error($this->CI->lang->line('authentication_error_email_invalid'));
            $valid = false;
        }
        if (strlen($pass) < $this->config_vars['min'] OR strlen($pass) > $this->config_vars['max']) {
            $this->error($this->CI->lang->line('authentication_error_password_invalid'));
            $valid = false;
        }
        if ($username != false && !ctype_alnum(str_replace($this->config_vars['additional_valid_chars'], '',
                $username))) {
            $this->error($this->CI->lang->line('authentication_error_username_invalid'));
            $valid = false;
        }
        if (!$valid) {
            return false;
        }


        $data = [
            'email' => $email,
            'password' => $this->hash_password($pass, 0),
            // Password cannot be blank but user_id required for salt, setting bad password for now
            'firstname' => $firstname,
            'lastname' => $lastname,
            'created_at' => date("Y-m-d H:i:s"),
        ];

        $insert = $this->CI->db->insert('customer', $data);
        

        if ($insert) {
            $user_id = $this->CI->db->insert_id();

            // set default group

            if (!$group_id) {
                $group_id = $this->get_group_id($this->config_vars['default_group']);
            }

            $this->add_member($user_id, $group_id);

            // if verification activated
            if ($this->config_vars['verification'] && !$this->is_admin()) {
                $data = null;
                $data['banned'] = 1;

                $this->CI->db->where('id', $user_id);
                $this->CI->db->update('customer', $data);

                // sends verifition ( !! e-mail settings must be set)
                $this->send_verification($user_id);
            }

            // Update to correct salted password
            if (!$this->config_vars['use_password_hash']) {
                $data = null;
                $data['password'] = $this->hash_password($pass, $user_id);
                $this->CI->db->where('id', $user_id);
                $this->CI->db->update('customer', $data);
            }

            return $user_id;

        } else {
            return false;
        }
    }

    /**
     * user_exist_by_username
     * Check if user exist by username
     * @param $user_id
     *
     * @return bool
     */
    public function user_exist_by_username($name)
    {
        $query = $this->CI->db->where('username', $name);

        $query = $this->CI->db->get('customer');

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * user_exist_by_email
     * Check if user exist by user email
     * @param $user_email
     *
     * @return bool
     */
    public function user_exist_by_email($user_email)
    {
        $query = $this->CI->db->where('email', $user_email);

        $query = $this->CI->db->get('customer');

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add member
     * Add a user to a group
     * @param int $user_id User id to add to group
     * @param int|string $group_par Group id or name to add user to
     * @return bool Add success/failure
     */
    public function add_member($user_id, $group_id = false)
    {

        if (!$group_id) {
            $this->error($this->CI->lang->line('authentication_error_no_group'));
            return false;
        }

        $query = $this->CI->db->where('user_id', $user_id);
        $query = $this->CI->db->where('group_id', $group_id);
        $query = $this->CI->db->get($this->config_vars['user_to_group']);

        if ($query->num_rows() < 1) {
            $data = [
                'user_id' => $user_id,
                'group_id' => $group_id
            ];

            return $this->CI->db->insert($this->config_vars['user_to_group'], $data);
        }
        $this->info($this->CI->lang->line('authentication_info_already_member'));
        return true;
    }

    //tested

    /**
     * Info
     *
     * Add message to info array and set flash data
     *
     * @param string $message Message to add to infos array
     * @param boolean $flashdata if true add $message to CI flashdata (deflault: false)
     */
    public function info($message = '', $flashdata = false)
    {
        $this->infos[] = $message;
        if ($flashdata) {
            $this->flash_infos[] = $message;
            $this->CI->session->set_flashdata('infos', $this->flash_infos);
        }
    }

    //tested

    /**
     * Send verification email
     * Sends a verification email based on user id
     * @param int $user_id User id to send verification email to
     * @todo return success indicator
     */
    public function send_verification($user_id)
    {

        $query = $this->CI->db->where('id', $user_id);
        $query = $this->CI->db->get('customer');

        if ($query->num_rows() > 0) {
            $row = $query->row();

            $ver_code = random_string('alnum', 16);

            $data['verification_code'] = $ver_code;

            $this->CI->db->where('id', $user_id);
            $this->CI->db->update('customer', $data);

            if (isset($this->config_vars['email_config']) && is_array($this->config_vars['email_config'])) {
                $this->CI->email->initialize($this->config_vars['email_config']);
            }

            $this->CI->email->from($this->config_vars['email'], $this->config_vars['name']);
            $this->CI->email->to($row->email);
            $this->CI->email->subject($this->CI->lang->line('authentication_email_verification_subject'));
            $this->CI->email->message($this->CI->lang->line('authentication_email_verification_code') . $ver_code .
                $this->CI->lang->line('authentication_email_verification_text') . site_url() . $this->config_vars['verification_link'] . $user_id . '/' . $ver_code);
            $this->CI->email->send();
        }
    }

    /**
     * Update user
     * Updates existing user details
     * @param int $user_id User id to update
     * @param string|bool $email User's email address, or false if not to be updated
     * @param string|bool $pass User's password, or false if not to be updated
     * @param string|bool $name User's name, or false if not to be updated
     * @return bool Update fails/succeeds
     */
    public function update_user(
        $user_id,
        $email = false,
        $pass = false,
        $username = false,
        $firstname = '',
        $lastname = '',
        $group_id = false
    ) {

        $data = [];
        $valid = true;
        $user = $this->get_user($user_id);

        if ($user->email == $email) {
            $email = false;
        }

        if ($email != false) {
            if ($this->user_exist_by_email($email)) {
                $this->error($this->CI->lang->line('authentication_error_update_email_exists'));
                $valid = false;
            }
            $valid_email = (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
            if (!$valid_email) {
                $this->error($this->CI->lang->line('authentication_error_email_invalid'));
                $valid = false;
            }
            $data['email'] = $email;
        }

        if ($pass != false) {
            if (strlen($pass) < $this->config_vars['min'] OR strlen($pass) > $this->config_vars['max']) {
                $this->error($this->CI->lang->line('authentication_error_password_invalid'));
                $valid = false;
            }
            $data['password'] = $this->hash_password($pass, $user_id);
        }

        if ($user->username == $username) {
            $username = false;
        }

        if ($username != false) {
            if ($this->user_exist_by_username($username)) {
                $this->error($this->CI->lang->line('authentication_error_update_username_exists'));
                $valid = false;
            }
            if ($username != '' && !ctype_alnum(str_replace($this->config_vars['additional_valid_chars'], '',
                    $username))) {
                $this->error($this->CI->lang->line('authentication_error_username_invalid'));
                $valid = false;
            }
            $data['username'] = $username;
        }

        if ($firstname != false) {
            $data['firstname'] = $firstname;
        }

        if ($lastname != false) {
            $data['lastname'] = $lastname;
        }

        if (!$valid || empty($data)) {
            return false;
        }

        //edit user group
        if ($group_id) {
            $this->edit_member($user_id, ['group_id' => $group_id]);
        }

        $this->CI->db->where('id', $user_id);
        return $this->CI->db->update('customer', $data);
    }

    ########################
    # Group Functions
    ########################

    //tested

    /**
     * Get user
     * Get user information
     * @param int|bool $user_id User id to get or false for current user
     * @return object User information
     */
    public function get_customer($customer_id = false)
    {
        if ($customer_id == false) {
            $customer_id = $this->CI->session->userdata('customer_id');
        }

        if($customer_id) {
            $query = $this->CI->db->where('id', $customer_id);
            $query = $this->CI->db->get('customer');

            if ($query->num_rows() <= 0) {
                $this->error($this->CI->lang->line('form_error_no_user'));
                return false;
            }

            return $query->row();
        }
        return false;
        
    }

    //tested

    /**
     * Add member
     * Add a user to a group
     * @param int $user_id User id to add to group
     * @param int|string $group_par Group id or name to add user to
     * @return bool Add success/failure
     */
    public function edit_member($user_id, $data)
    {

        $this->CI->db->where('user_id', $user_id);
        return $this->CI->db->update($this->config_vars['user_to_group'], $data);
    }

    //tested

    /**
     * List users
     * Return users as an object array
     * @param bool|int $group_par Specify group id to list group or false for all users
     * @param string $limit Limit of users to be returned
     * @param bool $offset Offset for limited number of users
     * @param bool $include_banneds Include banned users
     * @param string $sort Order by MYSQL string (e.g. 'name ASC', 'email DESC')
     * @return array Array of users
     */
    public function list_users(
        $group_par = false,
        $limit = false,
        $offset = false,
        $include_banneds = false,
        $sort = false
    ) {

        // if group_par is given
        if ($group_par != false) {

            $group_par = $this->get_group_id($group_par);
            $this->CI->db->select('*')
                ->from('customer')
                ->join($this->config_vars['user_to_group'],
                    'customer' . ".id = " . $this->config_vars['user_to_group'] . ".user_id")
                ->where($this->config_vars['user_to_group'] . ".group_id", $group_par);

            // if group_par is not given, lists all users
        } else {

            $this->CI->db->select('*')
                ->from('customer');
        }

        // banneds
        if (!$include_banneds) {
            $this->CI->db->where('banned != ', 1);
        }

        // order_by
        if ($sort) {
            $this->CI->db->order_by($sort);
        }

        // limit
        if ($limit) {

            if ($offset == false) {
                $this->CI->db->limit($limit);
            } else {
                $this->CI->db->limit($limit, $offset);
            }
        }

        $query = $this->CI->db->get();

        return $query->result();
    }

    //tested

    /**
     * Verify user
     * Activates user account based on verification code
     * @param int $user_id User id to activate
     * @param string $ver_code Code to validate against
     * @return bool Activation fails/succeeds
     */
    public function verify_user($user_id, $ver_code)
    {

        $query = $this->CI->db->where('id', $user_id);
        $query = $this->CI->db->where('verification_code', $ver_code);
        $query = $this->CI->db->get('customer');

        // if ver code is true
        if ($query->num_rows() > 0) {

            $data = [
                'verification_code' => '',
                'banned' => 0
            ];

            $this->CI->db->where('id', $user_id);
            $this->CI->db->update('customer', $data);
            return true;
        }
        return false;
    }

    //tested

    /**
     * Delete user
     * Delete a user from database. WARNING Can't be undone
     * @param int $user_id User id to delete
     * @return bool Delete fails/succeeds
     */
    public function delete_user($user_id)
    {

        // delete from perm_to_user
        $this->CI->db->where('user_id', $user_id);
        $this->CI->db->delete($this->config_vars['perm_to_user']);

        // delete from user_to_group
        $this->CI->db->where('user_id', $user_id);
        $this->CI->db->delete($this->config_vars['user_to_group']);

        // delete user vars
        $this->CI->db->where('user_id', $user_id);
        $this->CI->db->delete($this->config_vars['user_variables']);

        // delete user
        $this->CI->db->where('id', $user_id);
        return $this->CI->db->delete('customer');

    }

    //tested

    /**
     * Ban user
     * Bans a user account
     * @param int $user_id User id to ban
     * @return bool Ban fails/succeeds
     */
    public function ban_user($user_id)
    {

        $data = [
            'banned' => 1,
            'verification_code' => ''
        ];

        $this->CI->db->where('id', $user_id);

        return $this->CI->db->update('customer', $data);
    }

    /**
     * Unban user
     * Activates user account
     * Same with unlock_user()
     * @param int $user_id User id to activate
     * @return bool Activation fails/succeeds
     */
    public function unban_user($user_id)
    {

        $data = [
            'banned' => 0
        ];

        $this->CI->db->where('id', $user_id);

        return $this->CI->db->update('customer', $data);
    }

    /**
     * Get user id
     * Get user id from email address, if par. not given, return current user's id
     * @param string|bool $email Email address for user
     * @return int User id
     */
    public function get_user_id($email = false)
    {

        if (!$email) {
            $query = $this->CI->db->where('id', $this->CI->session->userdata('id'));
        } else {
            $query = $this->CI->db->where('email', $email);
        }

        $query = $this->CI->db->get('customer');

        if ($query->num_rows() <= 0) {
            $this->error($this->CI->lang->line('authentication_error_no_user'));
            return false;
        }
        return $query->row()->id;
    }

    //tested

    /**
     * Get user permissions
     * Get user permissions from user id ( ! Case sensitive)
     * @param int|bool $user_id User id to get or false for current user
     * @return int Group id
     */
    public function get_user_perms($user_id = false)
    {
        if (!$user_id) {
            $user_id = $this->CI->session->userdata('id');
        }

        if ($user_id) {
            $query = $this->CI->db->select($this->config_vars['perms'] . '.*');
            $query = $this->CI->db->where('user_id', $user_id);
            $query = $this->CI->db->join($this->config_vars['perms'],
                $this->config_vars['perms'] . '.id = ' . $this->config_vars['perm_to_user'] . '.perm_id');
            $query = $this->CI->db->get($this->config_vars['perm_to_user']);

            return $query->result();
        }

        return false;
    }
    //tested

    /**
     * Create group
     * Creates a new group
     * @param string $group_name New group name
     * @param string $definition Description of the group
     * @return int|bool Group id or false on fail
     */
    public function create_group($group_name, $definition = '')
    {

        $query = $this->CI->db->get_where($this->config_vars['groups'], ['name' => $group_name]);

        if ($query->num_rows() < 1) {

            $data = [
                'name' => $group_name,
                'definition' => $definition
            ];

            $this->CI->db->insert($this->config_vars['groups'], $data);
            return $this->CI->db->insert_id();
        }

        $this->info($this->CI->lang->line('authentication_info_group_exists'));
        return false;
    }

    //tested

    /**
     * Update group
     * Change a groups name
     * @param int $group_id Group id to update
     * @param string $group_name New group name
     * @return bool Update success/failure
     */
    public function update_group($group_par, $group_name = false, $definition = false)
    {

        $group_id = $this->get_group_id($group_par);

        if ($group_name != false) {
            $data['name'] = $group_name;
        }

        if ($definition != false) {
            $data['definition'] = $definition;
        }


        $this->CI->db->where('id', $group_id);
        return $this->CI->db->update($this->config_vars['groups'], $data);
    }


    //tested

    /**
     * Delete group
     * Delete a group from database. WARNING Can't be undone
     * @param int $group_id User id to delete
     * @return bool Delete success/failure
     */
    public function delete_group($group_par)
    {

        $group_id = $this->get_group_id($group_par);

        $this->CI->db->where('id', $group_id);
        $query = $this->CI->db->get($this->config_vars['groups']);
        if ($query->num_rows() == 0) {
            return false;
        }

        // bug fixed
        // now users are deleted from user_to_group table
        $this->CI->db->where('group_id', $group_id);
        $this->CI->db->delete($this->config_vars['user_to_group']);

        $this->CI->db->where('group_id', $group_id);
        $this->CI->db->delete($this->config_vars['perm_to_group']);

        $this->CI->db->where('group_id', $group_id);
        $this->CI->db->delete($this->config_vars['group_to_group']);

        $this->CI->db->where('subgroup_id', $group_id);
        $this->CI->db->delete($this->config_vars['group_to_group']);

        $this->CI->db->where('id', $group_id);
        return $this->CI->db->delete($this->config_vars['groups']);
    }

    //tested

    /**
     * Remove member
     * Remove a user from a group
     * @param int $user_id User id to remove from group
     * @param int|string $group_par Group id or name to remove user from
     * @return bool Remove success/failure
     */
    public function remove_member($user_id, $group_par)
    {

        $group_par = $this->get_group_id($group_par);
        $this->CI->db->where('user_id', $user_id);
        $this->CI->db->where('group_id', $group_par);
        return $this->CI->db->delete($this->config_vars['user_to_group']);
    }

    /**
     * Add subgroup
     * Add a subgroup to a group
     * @param int $user_id User id to add to group
     * @param int|string $group_par Group id or name to add user to
     * @return bool Add success/failure
     */
    public function add_subgroup($group_par, $subgroup_par)
    {

        $group_id = $this->get_group_id($group_par);
        $subgroup_id = $this->get_group_id($subgroup_par);

        if (!$group_id) {
            $this->error($this->CI->lang->line('authentication_error_no_group'));
            return false;
        }

        if (!$subgroup_id) {
            $this->error($this->CI->lang->line('authentication_error_no_subgroup'));
            return false;
        }

        $query = $this->CI->db->where('group_id', $group_id);
        $query = $this->CI->db->where('subgroup_id', $subgroup_id);
        $query = $this->CI->db->get($this->config_vars['group_to_group']);

        if ($query->num_rows() < 1) {
            $data = [
                'group_id' => $group_id,
                'subgroup_id' => $subgroup_id,
            ];

            return $this->CI->db->insert($this->config_vars['group_to_group'], $data);
        }
        $this->info($this->CI->lang->line('authentication_info_already_subgroup'));
        return true;
    }

    /**
     * Remove subgroup
     * Remove a subgroup from a group
     * @param int|string $group_par Group id or name to remove
     * @param int|string $subgroup_par Sub-Group id or name to remove
     * @return bool Remove success/failure
     */
    public function remove_subgroup($group_par, $subgroup_par)
    {

        $group_par = $this->get_group_id($group_par);
        $subgroup_par = $this->get_group_id($subgroup_par);
        $this->CI->db->where('group_id', $group_par);
        $this->CI->db->where('subgroup_id', $subgroup_par);
        return $this->CI->db->delete($this->config_vars['group_to_group']);
    }

    /**
     * Remove member
     * Remove a user from all groups
     * @param int $user_id User id to remove from all groups
     * @return bool Remove success/failure
     */
    public function remove_member_from_all($user_id)
    {

        $this->CI->db->where('user_id', $user_id);
        return $this->CI->db->delete($this->config_vars['user_to_group']);
    }

    ########################
    # Permission Functions
    ########################

    //tested

    /**
     * Get group name
     * Get group name from group id
     * @param int $group_id Group id to get
     * @return string Group name
     */
    public function get_group_name($group_id)
    {

        $query = $this->CI->db->where('id', $group_id);
        $query = $this->CI->db->get($this->config_vars['groups']);

        if ($query->num_rows() == 0) {
            return false;
        }

        $row = $query->row();
        return $row->name;
    }

    //tested

    /**
     * Get group
     * Get group from group name or id ( ! Case sensitive)
     * @param int|string $group_par Group id or name to get
     * @return int Group id
     */
    public function get_group($group_par)
    {
        if ($group_id = $this->get_group_id($group_par)) {
            $query = $this->CI->db->where('id', $group_id);
            $query = $this->CI->db->get($this->config_vars['groups']);

            return $query->row();
        }

        return false;
    }

    //not ok

    /**
     * Get group permissions
     * Get group permissions from group name or id ( ! Case sensitive)
     * @param int|string $group_par Group id or name to get
     * @return int Group id
     */
    public function get_group_perms($group_par)
    {
        if ($group_id = $this->get_group_id($group_par)) {
            $query = $this->CI->db->select($this->config_vars['perms'] . '.*');
            $query = $this->CI->db->where('group_id', $group_id);
            $query = $this->CI->db->join($this->config_vars['perms'],
                $this->config_vars['perms'] . '.id = ' . $this->config_vars['perm_to_group'] . '.perm_id');
            $query = $this->CI->db->get($this->config_vars['perm_to_group']);

            return $query->result();
        }

        return false;
    }

    /**
     * Create permission
     * Creates a new permission type
     * @param string $perm_name New permission name
     * @param string $definition Permission description
     * @return int|bool Permission id or false on fail
     */
    public function create_perm($perm_name, $definition = '')
    {

        $query = $this->CI->db->get_where($this->config_vars['perms'], ['name' => $perm_name]);

        if ($query->num_rows() < 1) {

            $data = [
                'name' => $perm_name,
                'definition' => $definition
            ];

            $this->CI->db->insert($this->config_vars['perms'], $data);
            return $this->CI->db->insert_id();
        }
        $this->info($this->CI->lang->line('authentication_info_perm_exists'));
        return false;
    }

    /**
     * Update permission
     * Updates permission name and description
     * @param int|string $perm_par Permission id or permission name
     * @param string $perm_name New permission name
     * @param string $definition Permission description
     * @return bool Update success/failure
     */
    public function update_perm($perm_par, $perm_name = false, $definition = false)
    {

        $perm_id = $this->get_perm_id($perm_par);

        if ($perm_name != false) {
            $data['name'] = $perm_name;
        }

        if ($definition != false) {
            $data['definition'] = $definition;
        }

        $this->CI->db->where('id', $perm_id);
        return $this->CI->db->update($this->config_vars['perms'], $data);
    }

    /**
     * Delete permission
     * Delete a permission from database. WARNING Can't be undone
     * @param int|string $perm_par Permission id or perm name to delete
     * @return bool Delete success/failure
     */
    public function delete_perm($perm_par)
    {

        $perm_id = $this->get_perm_id($perm_par);

        // deletes from perm_to_gropup table
        $this->CI->db->where('perm_id', $perm_id);
        $this->CI->db->delete($this->config_vars['perm_to_group']);

        // deletes from perm_to_user table
        $this->CI->db->where('perm_id', $perm_id);
        $this->CI->db->delete($this->config_vars['perm_to_user']);

        // deletes from permission table
        $this->CI->db->where('id', $perm_id);
        return $this->CI->db->delete($this->config_vars['perms']);
    }

    //tested

    /**
     * List Group Permissions
     * List all permissions by Group
     * @param int $group_par Group id or name to check
     * @return object Array of permissions
     */
    public function list_group_perms($group_par)
    {
        if (empty($group_par)) {
            return false;
        }

        $group_par = $this->get_group_id($group_par);

        $this->CI->db->select('*');
        $this->CI->db->from($this->config_vars['perms']);
        $this->CI->db->join($this->config_vars['perm_to_group'], "perm_id = " . $this->config_vars['perms'] . ".id");
        $this->CI->db->where($this->config_vars['perm_to_group'] . '.group_id', $group_par);

        $query = $this->CI->db->get();
        if ($query->num_rows() == 0) {
            return false;
        }

        return $query->result();
    }

    //tested

    /**
     * Allow User
     * Add User to permission
     * @param int $user_id User id to deny
     * @param int $perm_par Permission id or name to allow
     * @return bool Allow success/failure
     */
    public function allow_user($user_id, $perm_par)
    {

        $perm_id = $this->get_perm_id($perm_par);

        if (!$perm_id) {
            return false;
        }

        $query = $this->CI->db->where('user_id', $user_id);
        $query = $this->CI->db->where('perm_id', $perm_id);
        $query = $this->CI->db->get($this->config_vars['perm_to_user']);

        // if not inserted before
        if ($query->num_rows() < 1) {

            $data = [
                'user_id' => $user_id,
                'perm_id' => $perm_id
            ];

            return $this->CI->db->insert($this->config_vars['perm_to_user'], $data);
        }
        return true;
    }

    //tested

    /**
     * Deny User
     * Remove user from permission
     * @param int $user_id User id to deny
     * @param int $perm_par Permission id or name to deny
     * @return bool Deny success/failure
     */
    public function deny_user($user_id, $perm_par)
    {

        $perm_id = $this->get_perm_id($perm_par);

        $this->CI->db->where('user_id', $user_id);
        $this->CI->db->where('perm_id', $perm_id);

        return $this->CI->db->delete($this->config_vars['perm_to_user']);
    }

    //tested

    /**
     * Allow Group
     * Add group to permission
     * @param int|string|bool $group_par Group id or name to allow
     * @param int $perm_par Permission id or name to allow
     * @return bool Allow success/failure
     */
    public function allow_group($group_par, $perm_par)
    {

        $perm_id = $this->get_perm_id($perm_par);

        if (!$perm_id) {
            return false;
        }

        $group_id = $this->get_group_id($group_par);

        if (!$group_id) {
            return false;
        }

        $query = $this->CI->db->where('group_id', $group_id);
        $query = $this->CI->db->where('perm_id', $perm_id);
        $query = $this->CI->db->get($this->config_vars['perm_to_group']);

        if ($query->num_rows() < 1) {

            $data = [
                'group_id' => $group_id,
                'perm_id' => $perm_id
            ];

            return $this->CI->db->insert($this->config_vars['perm_to_group'], $data);
        }

        return true;
    }

    //tested

    /**
     * Deny Group
     * Remove group from permission
     * @param int|string|bool $group_par Group id or name to deny
     * @param int $perm_par Permission id or name to deny
     * @return bool Deny success/failure
     */
    public function deny_group($group_par, $perm_par)
    {

        $perm_id = $this->get_perm_id($perm_par);
        $group_id = $this->get_group_id($group_par);

        $this->CI->db->where('group_id', $group_id);
        $this->CI->db->where('perm_id', $perm_id);

        return $this->CI->db->delete($this->config_vars['perm_to_group']);
    }

    //tested

    /**
     * List Permissions
     * List all permissions
     * @return object Array of permissions
     */
    public function list_perms()
    {

        $query = $this->CI->db->get($this->config_vars['perms']);
        return $query->result();
    }

    /**
     * Get permission
     * Get permission from permisison name or id
     * @param int|string $perm_par Permission id or name to get
     * @return int Permission id or null if perm does not exist
     */
    public function get_perm($perm_par)
    {
        if ($perm_id = $this->get_perm_id($perm_par)) {
            $query = $this->CI->db->where('id', $perm_id);
            $query = $this->CI->db->get($this->config_vars['perms']);

            return $query->row();
        }

        return false;
    }

    ########################
    # Private Message Functions
    ########################

    //tested
    /**
     * Send Private Message
     * Send a private message to another user
     * @param int $sender_id User id of private message sender
     * @param int $receiver_id User id of private message receiver
     * @param string $title Message title/subject
     * @param string $message Message body/content
     * @return bool Send successful/failed
     */
    public function send_pm($sender_id, $receiver_id, $title, $message)
    {

        if (!is_numeric($receiver_id) OR $sender_id == $receiver_id) {
            $this->error($this->CI->lang->line('authentication_error_self_pm'));
            return false;
        }
        if (($this->is_banned($receiver_id) || !$this->user_exist_by_id($receiver_id)) || ($sender_id && ($this->is_banned($sender_id) || !$this->user_exist_by_id($sender_id)))) {
            $this->error($this->CI->lang->line('authentication_error_no_user'));
            return false;
        }
        if (!$sender_id) {
            $sender_id = 0;
        }

        if ($this->config_vars['pm_encryption']) {
            $title = $this->CI->encrypt->encode($title);
            $message = $this->CI->encrypt->encode($message);
        }

        $data = [
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'title' => $title,
            'message' => $message,
            'date_sent' => date('Y-m-d H:i:s')
        ];

        return $this->CI->db->insert($this->config_vars['pms'], $data);
    }

    /**
     * Check user banned
     * Checks if a user is banned
     * @param int $user_id User id to check
     * @return bool False if banned, True if not
     */
    public function is_banned($user_id)
    {

        $query = $this->CI->db->where('id', $user_id);
        $query = $this->CI->db->where('banned', 1);

        $query = $this->CI->db->get('customer');

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //tested

    /**
     * user_exist_by_id
     * Check if user exist by user email
     * @param $user_email
     *
     * @return bool
     */
    public function user_exist_by_id($user_id)
    {
        $query = $this->CI->db->where('id', $user_id);

        $query = $this->CI->db->get('customer');

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    //tested

    /**
     * Send multiple Private Messages
     * Send multiple private messages to another users
     * @param int $sender_id User id of private message sender
     * @param array $receiver_ids Array of User ids of private message receiver
     * @param string $title Message title/subject
     * @param string $message Message body/content
     * @return array/bool Array with User ID's as key and true or a specific error message OR false if sender doesn't exist
     */
    public function send_pms($sender_id, $receiver_ids, $title, $message)
    {
        if ($this->config_vars['pm_encryption']) {
            $title = $this->CI->encrypt->encode($title);
            $message = $this->CI->encrypt->encode($message);
        }
        if ($sender_id && ($this->is_banned($sender_id) || !$this->user_exist_by_id($sender_id))) {
            $this->error($this->CI->lang->line('authentication_error_no_user'));
            return false;
        }
        if (!$sender_id) {
            $sender_id = 0;
        }
        if (is_numeric($receiver_ids)) {
            $receiver_ids = [$receiver_ids];
        }

        $return_array = [];
        foreach ($receiver_ids as $receiver_id) {
            if ($sender_id == $receiver_id) {
                $return_array[$receiver_id] = $this->CI->lang->line('authentication_error_self_pm');
                continue;
            }
            if ($this->is_banned($receiver_id) || !$this->user_exist_by_id($receiver_id)) {
                $return_array[$receiver_id] = $this->CI->lang->line('authentication_error_no_user');
                continue;
            }

            $data = [
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'title' => $title,
                'message' => $message,
                'date_sent' => date('Y-m-d H:i:s')
            ];

            $return_array[$receiver_id] = $this->CI->db->insert($this->config_vars['pms'], $data);
        }

        return $return_array;
    }

    //tested

    /**
     * List Private Messages
     * If receiver id not given retruns current user's pms, if sender_id given, it returns only pms from given sender
     * @param int $limit Number of private messages to be returned
     * @param int $offset Offset for private messages to be returned (for pagination)
     * @param int $sender_id User id of private message sender
     * @param int $receiver_id User id of private message receiver
     * @return object Array of private messages
     */
    public function list_pms($limit = 5, $offset = 0, $receiver_id = null, $sender_id = null)
    {
        if (is_numeric($receiver_id)) {
            $query = $this->CI->db->where('receiver_id', $receiver_id);
            $query = $this->CI->db->where('pm_deleted_receiver', null);
        }
        if (is_numeric($sender_id)) {
            $query = $this->CI->db->where('sender_id', $sender_id);
            $query = $this->CI->db->where('pm_deleted_sender', null);
        }

        $query = $this->CI->db->order_by('id', 'DESC');
        $query = $this->CI->db->get($this->config_vars['pms'], $limit, $offset);

        $result = $query->result();

        if ($this->config_vars['pm_encryption']) {

            foreach ($result as $k => $r) {
                $result[$k]->title = $this->CI->encrypt->decode($r->title);
                $result[$k]->message = $this->CI->encrypt->decode($r->message);
            }
        }

        return $result;
    }

    /**
     * Get Private Message
     * Get private message by id
     * @param int $pm_id Private message id to be returned
     * @param int $user_id User ID of Sender or Receiver
     * @param bool $set_as_read Whether or not to mark message as read
     * @return object Private message
     */
    public function get_pm($pm_id, $user_id = null, $set_as_read = true)
    {
        if (!$user_id) {
            $user_id = $this->CI->session->userdata('id');
        }
        if (!is_numeric($user_id) || !is_numeric($pm_id)) {
            $this->error($this->CI->lang->line('authentication_error_no_pm'));
            return false;
        }

        $query = $this->CI->db->where('id', $pm_id);
        $query = $this->CI->db->group_start();
        $query = $this->CI->db->where('receiver_id', $user_id);
        $query = $this->CI->db->or_where('sender_id', $user_id);
        $query = $this->CI->db->group_end();
        $query = $this->CI->db->get($this->config_vars['pms']);

        if ($query->num_rows() < 1) {
            $this->error($this->CI->lang->line('authentication_error_no_pm'));
            return false;
        }

        $result = $query->row();

        if ($user_id == $result->receiver_id && $set_as_read) {
            $this->set_as_read_pm($pm_id);
        }

        if ($this->config_vars['pm_encryption']) {
            $result->title = $this->CI->encrypt->decode($result->title);
            $result->message = $this->CI->encrypt->decode($result->message);
        }

        return $result;
    }

    //tested

    /**
     * Set Private Message as read
     * Set private message as read
     * @param int $pm_id Private message id to mark as read
     */
    public function set_as_read_pm($pm_id)
    {

        $data = [
            'date_read' => date('Y-m-d H:i:s')
        ];

        $this->CI->db->update($this->config_vars['pms'], $data, "id = $pm_id");
    }

    //tested

    /**
     * Delete Private Message
     * Delete private message by id
     * @param int $pm_id Private message id to be deleted
     * @return bool Delete success/failure
     */
    public function delete_pm($pm_id, $user_id = null)
    {
        if (!$user_id) {
            $user_id = $this->CI->session->userdata('id');
        }
        if (!is_numeric($user_id) || !is_numeric($pm_id)) {
            $this->error($this->CI->lang->line('authentication_error_no_pm'));
            return false;
        }

        $query = $this->CI->db->where('id', $pm_id);
        $query = $this->CI->db->group_start();
        $query = $this->CI->db->where('receiver_id', $user_id);
        $query = $this->CI->db->or_where('sender_id', $user_id);
        $query = $this->CI->db->group_end();
        $query = $this->CI->db->get($this->config_vars['pms']);
        $result = $query->row();
        if ($user_id == $result->sender_id) {
            if ($result->pm_deleted_receiver == 1) {
                return $this->CI->db->delete($this->config_vars['pms'], ['id' => $pm_id]);
            }

            return $this->CI->db->update($this->config_vars['pms'], ['pm_deleted_sender' => 1], ['id' => $pm_id]);
        } else {
            if ($user_id == $result->receiver_id) {
                if ($result->pm_deleted_sender == 1) {
                    return $this->CI->db->delete($this->config_vars['pms'], ['id' => $pm_id]);
                }

                return $this->CI->db->update($this->config_vars['pms'],
                    ['pm_deleted_receiver' => 1, 'date_read' => date('Y-m-d H:i:s')], ['id' => $pm_id]);
            }
        }
    }

    ########################
    # Error / Info Functions
    ########################

    /**
     * Cleanup PMs
     * Removes PMs older than 'pm_cleanup_max_age' (definied in auth config).
     * recommend for a cron job
     */
    public function cleanup_pms()
    {
        $pm_cleanup_max_age = $this->config_vars['pm_cleanup_max_age'];
        $date_sent = date('Y-m-d H:i:s', strtotime("now -" . $pm_cleanup_max_age));
        $this->CI->db->where('date_sent <', $date_sent);

        return $this->CI->db->delete($this->config_vars['pms']);
    }

    /**
     * Count unread Private Message
     * Count number of unread private messages
     * @param int|bool $receiver_id User id for message receiver, if false returns for current user
     * @return int Number of unread messages
     */
    public function count_unread_pms($receiver_id = false)
    {

        if (!$receiver_id) {
            $receiver_id = $this->CI->session->userdata('id');
        }

        $query = $this->CI->db->where('receiver_id', $receiver_id);
        $query = $this->CI->db->where('date_read', null);
        $query = $this->CI->db->where('pm_deleted_sender', null);
        $query = $this->CI->db->where('pm_deleted_receiver', null);
        $query = $this->CI->db->get($this->config_vars['pms']);

        return $query->num_rows();
    }

    //tested

    /**
     * Keep Errors
     *
     * Keeps the flashdata errors for one more page refresh.  Optionally adds the default errors into the
     * flashdata list.  This should be called last in your controller, and with care as it could continue
     * to revive all errors and not let them expire as intended.
     * Benefitial when using Ajax Requests
     * @see http://ellislab.com/codeigniter/user-guide/libraries/sessions.html
     * @param boolean $include_non_flash true if it should stow basic errors as flashdata (default = false)
     */
    public function keep_errors($include_non_flash = false)
    {
        // NOTE: keep_flashdata() overwrites anything new that has been added to flashdata so we are manually reviving flash data
        // $this->CI->session->keep_flashdata('errors');

        if ($include_non_flash) {
            $this->flash_errors = array_merge($this->flash_errors, $this->errors);
        }
        $this->flash_errors = array_merge($this->flash_errors, (array)$this->CI->session->flashdata('errors'));
        $this->CI->session->set_flashdata('errors', $this->flash_errors);
    }

    /**
     * Get Errors Array
     * Return array of errors
     * @return array Array of messages, empty array if no errors
     */
    public function get_errors_array()
    {
        return $this->errors;
    }

    /**
     * Print Errors
     *
     * Prints string of errors separated by delimiter
     * @param string $divider Separator for errors
     */
    public function print_errors($divider = '<br />')
    {
        $msg = '';
        $msg_num = count($this->errors);
        $i = 1;
        foreach ($this->errors as $e) {
            $msg .= $e;

            if ($i != $msg_num) {
                $msg .= $divider;
            }
            $i++;
        }
        return $msg;
    }

    /**
     * Clear Errors
     *
     * Removes errors from error list and clears all associated flashdata
     */
    public function clear_errors()
    {
        $this->errors = [];
        $this->CI->session->set_flashdata('errors', $this->errors);
    }

    /**
     * Keep Infos
     *
     * Keeps the flashdata infos for one more page refresh.  Optionally adds the default infos into the
     * flashdata list.  This should be called last in your controller, and with care as it could continue
     * to revive all infos and not let them expire as intended.
     * Benefitial by using Ajax Requests
     * @see http://ellislab.com/codeigniter/user-guide/libraries/sessions.html
     * @param boolean $include_non_flash true if it should stow basic infos as flashdata (default = false)
     */
    public function keep_infos($include_non_flash = false)
    {
        // NOTE: keep_flashdata() overwrites anything new that has been added to flashdata so we are manually reviving flash data
        // $this->CI->session->keep_flashdata('infos');

        if ($include_non_flash) {
            $this->flash_infos = array_merge($this->flash_infos, $this->infos);
        }
        $this->flash_infos = array_merge($this->flash_infos, (array)$this->CI->session->flashdata('infos'));
        $this->CI->session->set_flashdata('infos', $this->flash_infos);
    }

    /**
     * Get Info Array
     *
     * Return array of infos
     * @return array Array of messages, empty array if no errors
     */
    public function get_infos_array()
    {
        return $this->infos;
    }


    /**
     * Print Info
     *
     * Print string of info separated by delimiter
     * @param string $divider Separator for info
     *
     */
    public function print_infos($divider = '<br />')
    {

        $msg = '';
        $msg_num = count($this->infos);
        $i = 1;
        foreach ($this->infos as $e) {
            $msg .= $e;

            if ($i != $msg_num) {
                $msg .= $divider;
            }
            $i++;
        }
        echo $msg;
    }

    /**
     * Clear Info List
     *
     * Removes info messages from info list and clears all associated flashdata
     */
    public function clear_infos()
    {
        $this->infos = [];
        $this->CI->session->set_flashdata('infos', $this->infos);
    }

    ########################
    # User Variables
    ########################

    //tested
    /**
     * Set User Variable as key value
     * if variable not set before, it will ve set
     * if set, overwrites the value
     * @param string $key
     * @param string $value
     * @param int $user_id ; if not given current user
     * @return bool
     */
    public function set_user_var($key, $value, $user_id = false)
    {

        if (!$user_id) {
            $user_id = $this->CI->session->userdata('id');
        }

        // if specified user is not found
        if (!$this->get_user($user_id)) {
            return false;
        }

        // if var not set, set
        if ($this->get_user_var($key, $user_id) === false) {

            $data = [
                'data_key' => $key,
                'value' => $value,
                'user_id' => $user_id
            ];

            return $this->CI->db->insert($this->config_vars['user_variables'], $data);
        } // if var already set, overwrite
        else {

            $data = [
                'data_key' => $key,
                'value' => $value,
                'user_id' => $user_id
            ];

            $this->CI->db->where('data_key', $key);
            $this->CI->db->where('user_id', $user_id);

            return $this->CI->db->update($this->config_vars['user_variables'], $data);
        }
    }

    //tested

    /**
     * Get User Variable by key
     * Return string of variable value or false
     * @param string $key
     * @param int $user_id ; if not given current user
     * @return bool|string , false if var is not set, the value of var if set
     */
    public function get_user_var($key, $user_id = false)
    {

        if (!$user_id) {
            $user_id = $this->CI->session->userdata('id');
        }

        // if specified user is not found
        if (!$this->get_user($user_id)) {
            return false;
        }

        $query = $this->CI->db->where('user_id', $user_id);
        $query = $this->CI->db->where('data_key', $key);

        $query = $this->CI->db->get($this->config_vars['user_variables']);

        // if variable not set
        if ($query->num_rows() < 1) {
            return false;
        } else {

            $row = $query->row();
            return $row->value;
        }

    }

    //tested

    /**
     * Unset User Variable as key value
     * @param string $key
     * @param int $user_id ; if not given current user
     * @return bool
     */
    public function unset_user_var($key, $user_id = false)
    {

        if (!$user_id) {
            $user_id = $this->CI->session->userdata('id');
        }

        // if specified user is not found
        if (!$this->get_user($user_id)) {
            return false;
        }

        $this->CI->db->where('data_key', $key);
        $this->CI->db->where('user_id', $user_id);

        return $this->CI->db->delete($this->config_vars['user_variables']);
    }

    /**
     * Get User Variables by user id
     * Return array with all user keys & variables
     * @param int $user_id ; if not given current user
     * @return bool|array , false if var is not set, the value of var if set
     */
    public function get_user_vars($user_id = false)
    {

        if (!$user_id) {
            $user_id = $this->CI->session->userdata('id');
        }

        // if specified user is not found
        if (!$this->get_user($user_id)) {
            return false;
        }

        $query = $this->CI->db->select('data_key, value');

        $query = $this->CI->db->where('user_id', $user_id);

        $query = $this->CI->db->get($this->config_vars['user_variables']);

        return $query->result();

    }

    /**
     * List User Variable Keys by UserID
     * Return array of variable keys or false
     * @param int $user_id ; if not given current user
     * @return bool|array, false if no user vars, otherwise array
     */
    public function list_user_var_keys($user_id = false)
    {

        if (!$user_id) {
            $user_id = $this->CI->session->userdata('id');
        }

        // if specified user is not found
        if (!$this->get_user($user_id)) {
            return false;
        }
        $query = $this->CI->db->select('data_key');

        $query = $this->CI->db->where('user_id', $user_id);

        $query = $this->CI->db->get($this->config_vars['user_variables']);

        // if variable not set
        if ($query->num_rows() < 1) {
            return false;
        } else {
            return $query->result();
        }
    }

    public function generate_recaptcha_field()
    {
        $content = '';
        if ($this->config_vars['ddos_protection'] && $this->config_vars['recaptcha_active'] && $this->get_login_attempts() >= $this->config_vars['recaptcha_login_attempts']) {
            $content .= "<script type='text/javascript' src='https://www.google.com/recaptcha/api.js'></script>";
            $siteKey = $this->config_vars['recaptcha_siteKey'];
            $content .= "<div class='g-recaptcha' data-sitekey='{$siteKey}'></div>";
        }
        return $content;
    }

    public function update_user_totp_secret($user_id = false, $secret)
    {

        if ($user_id == false) {
            $user_id = $this->CI->session->userdata('id');
        }

        $data['totp_secret'] = $secret;

        $this->CI->db->where('id', $user_id);
        return $this->CI->db->update('customer', $data);
    }

    public function generate_unique_totp_secret()
    {
        $this->CI->load->helper('googleauthenticator');
        $ga = new PHPGangsta_GoogleAuthenticator();
        $stop = false;
        while (!$stop) {
            $secret = $ga->createSecret();
            $query = $this->CI->db->where('totp_secret', $secret);
            $query = $this->CI->db->get('customer');
            if ($query->num_rows() == 0) {
                return $secret;
                $stop = true;
            }
        }
    }

    public function generate_totp_qrcode($secret)
    {
        $this->CI->load->helper('googleauthenticator');
        $ga = new PHPGangsta_GoogleAuthenticator();
        return $ga->getQRCodeGoogleUrl($this->config_vars['name'], $secret);
    }

    public function verify_user_totp_code($totp_code, $user_id = false)
    {
        if (!$this->is_totp_required()) {
            return true;
        }
        if ($user_id == false) {
            $user_id = $this->CI->session->userdata('id');
        }
        if (empty($totp_code)) {
            $this->error($this->CI->lang->line('authentication_error_totp_code_required'));
            return false;
        }
        $query = $this->CI->db->where('id', $user_id);
        $query = $this->CI->db->get('customer');
        $totp_secret = $query->row()->totp_secret;
        $this->CI->load->helper('googleauthenticator');
        $ga = new PHPGangsta_GoogleAuthenticator();
        $checkResult = $ga->verifyCode($totp_secret, $totp_code, 0);
        if (!$checkResult) {
            $this->error($this->CI->lang->line('authentication_error_totp_code_invalid'));
            return false;
        } else {
            $this->CI->session->unset_userdata('totp_required');
            return true;
        }
    }

    public function is_totp_required()
    {
        if (!$this->CI->session->userdata('totp_required')) {
            return false;
        } else {
            if ($this->CI->session->userdata('totp_required')) {
                return true;
            }
        }
    }

    public function asdf()
    {
        if ($data == true) {
            $asa = 'salam salaam';
        }
    }

} // end class

/* End of file auth.php */
/* Location: ./application/libraries/auth.php */
