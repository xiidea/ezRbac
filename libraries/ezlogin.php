<?php
/**
 * ezlogin class
 * A simple class adds the login feature to the package
 *
 * @version    1.2
 * @package    ezRbac
 * @since      ezRbac v 0.3
 * @author     Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright  Copyright &copy; 2012 Roni Saha
 * @license    GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class ezlogin
{
    /**
     * @var CI_Controller CI instance reference holder
     */
    private $CI;

    /**
     * @var string store error
     */
    private $error;

    private $_login_session_key = FALSE;

    private $_user_schema;

    /**
     *
     */
    function __construct()
    {
        $this->CI                 = & get_instance();
        $this->_login_session_key = $this->CI->config->item('login_session_key', 'ez_rbac');
        $this->_user_schema       = $this->CI->config->item('schema_user_table', 'ez_rbac');
    }


    /**
     * @param int $index identify the validation rule set
     *
     * @return array The validation rule for login,recovery and reset password form
     */
    private function validation_rule($index = 0)
    {
        $ret_arr = array(
            array(
                array(
                    'field' => 'username',
                    'label' => 'Email',
                    'rules' => 'trim|required|valid_email|xss_clean'
                ),
                array(
                    'field' => 'password',
                    'label' => 'Password',
                    'rules' => 'trim|required|xss_clean|min_length[' . $this->CI->config->item('password_min_length', 'ez_rbac') . ']'
                )
            ),
            array(
                array(
                    'field' => 'username',
                    'label' => 'Email',
                    'rules' => 'trim|required|valid_email|xss_clean'
                )
            ),
            array(
                array(
                    'field' => 'password',
                    'label' => 'New Password',
                    'rules' => 'trim|required|xss_clean|min_length[' . $this->CI->config->item('password_min_length', 'ez_rbac') . ']|matches[re_password]'
                ),
                array(
                    'field' => 're_password',
                    'label' => 'Re-Type Password',
                    'rules' => 'trim|required|xss_clean|min_length[' . $this->CI->config->item('password_min_length', 'ez_rbac') . ']'
                )
            ),
        );
        return $ret_arr[$index];
    }

    /**
     * handle login
     *
     * @access Public
     */
    public function login()
    {
        $this->CI->form_validation->set_rules($this->validation_rule());
        if ($this->CI->form_validation->run() == FALSE) {
            $this->error = validation_errors();
            $this->view_login_form();
        }
        if ($this->process_login()) {
            redirect($this->CI->uri->uri_string());
        }
        $this->view_login_form();
    }

    /**
     * Display the login form. if there is any error also show it
     *
     * @access Private
     */
    private function view_login_form()
    {
        $data['form_error'] = $this->error;
        $this->load_view('login/index', $data);
        $this->end_now();
    }

    /**
     * Proxy for the load->view() to enable template override
     * @param $view
     * @param $data
     * @param bool $return
     * @return mixed
     */
    private function load_view($view, $data, $return = false)
    {
        $this->CI->throw_exception = true;

        $view_ref = $this->get_view_ref($view);

        return $this->CI->load->view($view_ref, $data, $return);
    }

    /**
     * Return the template file. If override file available, return that template
     * @param $view
     * @return string
     */
    private function get_view_ref($view)
    {
        $_ci_ext = pathinfo($view, PATHINFO_EXTENSION);
        $overrideTemplate = 'ezrbac/' . $view;
        $_ci_file = ($_ci_ext == '') ? $overrideTemplate.'.php' : $overrideTemplate;

        return file_exists(APPPATH . 'views/' . $_ci_file) ? $overrideTemplate : $view;
    }

    /**
     * Display the reset password form. if there is any error also show it
     *
     * @access Private
     */
    private function view_password_reset_form()
    {
        $data['form_error'] = $this->error;
        $this->load_view('login/reset', $data);
        $this->end_now();
    }

    /**
     * Display the reset password form. if there is any error also show it
     *
     * @access Private
     */
    private function view_password_reset_message()
    {
        $data['reset_success'] = TRUE;
        $this->load_view('login/reset', $data);
        $this->end_now();
    }

    /**
     * validate and process the login
     *
     * @access private
     * @return bool
     */
    private function process_login()
    {
        $this->CI->load->model('ezuser');
        $useemail = $this->CI->input->post("username", TRUE);
        $password = $this->CI->input->post("password", TRUE);
        $remember = $this->CI->input->post("remember");

        if (!is_null($user = $this->CI->ezuser->get_user_by_email($useemail))) { //email ok

            // Does password match hash in database?
            if ($this->CI->encrypt->sha1($password . $user->{$this->_user_schema['salt']}) === $user->{$this->_user_schema['password']}) { // password ok
                return $this->register_session($user, $remember);
            }
            // fail - wrong password
            $this->error = 'Incorrect Password';
            return FALSE;
        } // fail - wrong login
        $this->error = 'User is not registered';
        return FALSE;

    }

    public function register_session($user, $remember = FALSE, $cookie = FALSE)
    {
        if ($user->verification_status == 0) { // fail - not verified
            $this->error = 'email is not verified';
            return FALSE;
        }
        if ($user->{$this->_user_schema['status']} == 0) { // fail - not activated
            $this->error = 'account is disabled! contact system administrator';
            return FALSE;
        }

        if (!$user->{$this->_user_schema['user_role_id']}) {
            $this->error = 'user role is not defined for this user';
            return FALSE;
        }

        $this->CI->session->set_userdata(array(
            'user_id'     => $user->{$this->_user_schema['id']},
            'user_email'  => $user->{$this->_user_schema['email']},
            'access_role' => $user->{$this->_user_schema['user_role_id']}
        ));

        if ($cookie) {
            // Renew users cookie to prevent it from expiring
            set_cookie(array(
                'name'   => $this->CI->config->item('autologin_cookie_name', 'ez_rbac'),
                'value'  => $cookie,
                'expire' => $this->CI->config->item('autologin_cookie_life', 'ez_rbac'),
            ));
        } elseif ($remember) {
            $this->create_autologin($user->{$this->_user_schema['id']});
        }
        $this->CI->ezuser->update_login_info($user->{$this->_user_schema['id']});
        return TRUE;
    }


    /**
     * Save data for user's autologin
     *
     * @param    int
     *
     * @return    bool
     */
    private function create_autologin($user_id)
    {
        $key = substr(md5(uniqid(rand() . get_cookie($this->CI->config->item('sess_cookie_name')))), 0, 16);

        $this->CI->load->model('user_autologin');
        $this->CI->user_autologin->purge($user_id);
        if ($this->CI->user_autologin->set($user_id, md5($key))) {
            set_cookie(array(
                'name'   => $this->CI->config->item('autologin_cookie_name', 'ez_rbac'),
                'value'  => serialize(array('user_id' => $user_id, 'key' => $key)),
                'expire' => $this->CI->config->item('autologin_cookie_life', 'ez_rbac'),
            ));
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Automatic login user form cookie value return false if no valid cookie information found and auto login faild
     *
     * @access private
     * @return bool
     */
    function auto_login()
    {
        if ($cookie = get_cookie($this->CI->config->item('autologin_cookie_name', 'ez_rbac'), TRUE)) {
            $data = unserialize($cookie);
            if (isset($data['key']) AND isset($data['user_id'])) {

                $this->CI->load->model('user_autologin');
                if (!is_null($user = $this->CI->user_autologin->get($data['user_id'], md5($data['key'])))) {
                    return $this->register_session($user, FALSE, $cookie);
                }
            }
        }
    }

    /**
     * Logout user from the site
     *
     * @return    void
     */
    function logout()
    {
        $this->delete_autologin();

        //making sure though the session data exist through the script execution  it must not be valid after logout
        $this->CI->session->set_userdata(array('user_id' => '', 'user_email' => '', 'access_role' => ''));
        $this->CI->session->sess_destroy();
    }

    /**
     * Clear user's autologin data
     *
     * @return    void
     */
    private function delete_autologin()
    {
        if ($cookie = get_cookie($this->CI->config->item('autologin_cookie_name', 'ez_rbac'), TRUE)) {

            $data = unserialize($cookie);

            $this->CI->load->model('user_autologin');
            $this->CI->user_autologin->delete($data['user_id'], md5($data['key']));

            delete_cookie($this->CI->config->item('autologin_cookie_name', 'ez_rbac'));
        }
    }


    /**
     * Recover Password Function
     * This function called when the password recovery form submited
     *
     */
    function recover_password()
    {
        $this->CI->form_validation->set_rules($this->validation_rule(1));
        if ($this->CI->form_validation->run() == FALSE) {
            $this->error = validation_errors();
            $this->view_login_form();
        }
        $useemail = trim($this->CI->input->post('username', TRUE));
        $this->CI->load->model('ezuser');
        if (is_null($user = $this->CI->ezuser->get_user_by_email($useemail))) { // user not found
            $this->error = 'Email address not registered!';
            $this->view_login_form();
        }
        if ($this->process_recovery($user)) {
            $data['reset_email_confirm'] = TRUE;
            $data['form_error']          = '';
            $this->load_view('login/index', $data);
            $this->end_now();
        }
        $this->view_login_form();
    }

    /**
     * validate and process the password recovery
     *
     * @access private
     *
     * @param $user
     *
     * @return bool
     */
    private function process_recovery($user)
    {
        $key         = $this->CI->ezuser->requestPassword($user->{$this->_user_schema['id']});
        $data['url'] = $this->CI->ezuri->RbacUrl("resetpassword/key/$key/e/" . rawurlencode($user->{$this->_user_schema['email']}));
        $email_body  = $this->load_view('login/_password_email', $data, TRUE);

        //Disable this while you running the script on server
        if ($this->CI->config->item('show_password_reset_mail', 'ez_rbac')) {
            die($email_body);
        }

        $option = array('subject'   => $this->CI->config->item('password_recovery_subject', 'ez_rbac'),
                        'from'      => $this->CI->config->item('password_recovery_email', 'ez_rbac'),
                        'from_name' => $this->CI->config->item('password_recovery_email_name', 'ez_rbac'),
                        'to'        => $user->email,
                        'body'      => $email_body);

        return $this->send_email($option);

    }


    /**
     * validate and reset the password
     *
     * @access public
     *
     * @param array $param
     *
     * @return boolean
     */
    public function resetPassword($param = array())
    {
        $key   = $param['key'];
        $email = rawurldecode($param['e']);
        $this->CI->load->model('ezuser');
        $user = $this->CI->ezuser->get_user_by_email($email);

        if ($user == NULL) {
            show_404();
        }

        if ($this->validateRequestHash($user, $key)) {

            if (isset($_POST['ResetForm'])) {
                $this->CI->form_validation->set_rules($this->validation_rule(2));
                if ($this->CI->form_validation->run() == FALSE) {
                    $this->error = validation_errors();
                    $this->view_password_reset_form();
                }

                $this->CI->load->model('ezuser');
                $password = $this->CI->input->post("password", TRUE);
                $this->CI->ezuser->set_new_password((string)$password, $email);
                $this->view_password_reset_message();
            }
            $this->view_password_reset_form();
        } else {
            show_404();
        }
    }

    /**
     * @param        $user
     * @param string $key
     *
     * @return bool
     */
    private function validateRequestHash($user, $key = "")
    {
        if ($key == "") {
            return FALSE;
        }
        $date = new DateTime($user->{$this->_user_schema['reset_request_time']});
        $date->modify("+2 day");
        return (md5($user->{$this->_user_schema['reset_request_code']} . $user->{$this->_user_schema['reset_request_time']} . $user->{$this->_user_schema['reset_request_ip']}) === $key && $date > new DateTime());
    }


    public function getError()
    {
        return $this->error;
    }

    /**
     * terminate the execution within the script! we will be stop here and
     * further execution will be stopped
     * I have not found anything to detect the exit , so doing it manually!!
     * Hope the piece of code will not be necessary when i figure it out!!!
     */
    private function end_now()
    {
        $this->CI->we_are_done = TRUE;
        exit;
    }

    /**
     * @param $option
     * @return mixed
     */
    protected function send_email($option)
    {
        $mailFunction = $this->CI->config->item('override_email_function', 'ez_rbac');

        if ($mailFunction && is_callable($mailFunction)) {
            return $mailFunction($option);
        }

        $this->CI->load->library('email');

        $this->CI->email->initialize(array(
            'mailtype' => 'html',
            'wordwrap' => FALSE,
        ));

        $this->CI->email->from($option['from'], $option['form_name']);
        $this->CI->email->to($option['to']);
        $this->CI->email->subject($option['subject']);
        $this->CI->email->message($option['body']);
        $this->CI->email->set_alt_message('View the mail using a html email client');

        return $this->CI->email->send();
    }

}

/* End of file ezlogin.php */
/* Location: ./ezRbac/libraries/ezlogin.php */