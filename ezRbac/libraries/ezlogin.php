<?php
/**
 * ezlogin class
 * A simple class adds the login feature to the package
 *
 * @version	1.1
 * @package ezRbac
 * @since ezRbac v 0.2
 * @author Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright Copyright &copy; 2012 Roni Saha
 * @license	GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
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

    /**
     *
     */
    function __construct()
    {
        $this->CI = & get_instance();
    }


    /**
     * @param int $index identify the validation rule set
     * @return array The validation rule for login,recovery and reset password form
     */
    private function validation_rule($index=0){
        $ret_arr= array(
                        array(
                            array(
                                'field'   => 'username',
                                'label'   => 'Email',
                                'rules'   => 'trim|required|valid_email|xss_clean'
                            ),
                            array(
                                'field'   => 'password',
                                'label'   => 'Password',
                                'rules'   => 'trim|required|xss_clean|min_length['.$this->CI->config->item('password_min_length', 'ez_rbac').']'
                            )
                        ),
                        array(
                            array(
                                'field'   => 'username',
                                'label'   => 'Email',
                                'rules'   => 'trim|required|valid_email|xss_clean'
                            )
                         ),
                        array(
                            array(
                                'field'   => 'password',
                                'label'   => 'New Password',
                                'rules'   => 'trim|required|xss_clean|min_length['.$this->CI->config->item('password_min_length', 'ez_rbac').']|matches[re_password]'
                            ),
                            array(
                                'field'   => 're_password',
                                'label'   => 'Re-Type Password',
                                'rules'   => 'trim|required|xss_clean|min_length['.$this->CI->config->item('password_min_length', 'ez_rbac').']'
                            )
                        ),
                     );
        return $ret_arr[$index];
    }

    /**
     * handle login
     * @access Public
     */
    public function login(){
        $this->CI->form_validation->set_rules($this->validation_rule());
        if ($this->CI->form_validation->run() == FALSE){
            $this->error=validation_errors();
            $this->view_login_form();
        }
        if($this->process_login()){
            redirect($this->CI->uri->uri_string());
        }
        $this->view_login_form();
    }

    /**
     * Display the login form. if there is any error also show it
     * @access Private
     */
    private function view_login_form(){
        $data['form_error'] = $this->error;
        echo $this->CI->load->view('login/index', $data,true);
        $this->end_now();
    }

    /**
     * Display the reset password form. if there is any error also show it
     * @access Private
     */
    private function view_password_reset_form(){
        $data['form_error'] = $this->error;
        echo $this->CI->load->view('login/reset', $data,true);

        $this->end_now();
    }

    /**
     * Display the reset password form. if there is any error also show it
     * @access Private
     */
    private function view_password_reset_message(){
        $data['reset_success'] = true;
        echo $this->CI->load->view('login/reset', $data,true);
        $this->end_now();
    }

    /**
     * validate and process the login
     * @access private
     * @return bool
     */
    private function process_login(){
        $this->CI->load->model('ezuser');
        $useemail=$this->CI->input->post("username",TRUE);
        $password=$this->CI->input->post("password",TRUE);
        $remember=$this->CI->input->post("remember");

        if (!is_null($user = $this->CI->ezuser->get_user_by_email($useemail))) {	//email ok

            // Does password match hash in database?
            if ($this->CI->encrypt->sha1($password.$user->salt)===$user->password){		// password ok

                $this->CI->session->set_userdata(array(
                    'user_id'	=> $user->id,
                    'user_email'	=> $user->email,
                    'access_role'	=> $user->user_role_id
                ));

                if ($user->verification_status == 0) {							// fail - not activated
                    $this->error = 'email is not verified';
                    return false;
                }
                												// success
                if ($remember) {
                    $this->create_autologin($user->id);
                }
                $this->CI->ezuser->update_login_info($user->id);
                return TRUE;
            } 														// fail - wrong password
            $this->error =  'Incorrect Password';
            return false;
        }        															// fail - wrong login
        $this->error =  'User is not registered';
        return false;

    }


    /**
     * Save data for user's autologin
     *
     * @param	int
     * @return	bool
     */
    private function create_autologin($user_id)
    {
        $key = substr(md5(uniqid(rand().get_cookie($this->CI->config->item('sess_cookie_name')))), 0, 16);

        $this->CI->load->model('user_autologin');
        $this->CI->user_autologin->purge($user_id);
        if ($this->CI->user_autologin->set($user_id, md5($key))) {
            set_cookie(array(
                'name' 		=> $this->CI->config->item('autologin_cookie_name', 'ez_rbac'),
                'value'		=> serialize(array('user_id' => $user_id, 'key' => $key)),
                'expire'	=> $this->CI->config->item('autologin_cookie_life', 'ez_rbac'),
            ));
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Automatic login user form cookie value return false if no valid cookie information found and auto login faild
     * @access private
     * @return bool
     */
    function auto_login(){
        if ($cookie = get_cookie($this->CI->config->item('autologin_cookie_name', 'ez_rbac'), TRUE)) {
            $data = unserialize($cookie);
            if (isset($data['key']) AND isset($data['user_id'])) {

                $this->CI->load->model('user_autologin');
                if (!is_null($user = $this->CI->user_autologin->get($data['user_id'], md5($data['key'])))) {

                    // Login user
                    $this->CI->session->set_userdata(array(
                        'user_id'	=> $user->id,
                        'user_email'	=> $user->email,
                        'access_role'	=> $user->user_role_id,
                    ));

                    // Renew users cookie to prevent it from expiring
                    set_cookie(array(
                        'name' 		=> $this->CI->config->item('autologin_cookie_name', 'ez_rbac'),
                        'value'		=> $cookie,
                        'expire'	=> $this->CI->config->item('autologin_cookie_life', 'ez_rbac'),
                    ));

                    $this->CI->load->model('ezuser');
                    $this->CI->ezuser->update_login_info($user->id);
                    return TRUE;
                }
            }
        }
    }

    /**
     * Logout user from the site
     *
     * @return	void
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
     * @return	void
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
    function recover_password(){
        $this->CI->form_validation->set_rules($this->validation_rule(1));
        if ($this->CI->form_validation->run() == FALSE){
            $this->error=validation_errors();
            $this->view_login_form();
        }
        $useemail = trim($this->CI->input->post('username',TRUE));
        $this->CI->load->model('ezuser');
        if (is_null($user = $this->CI->ezuser->get_user_by_email($useemail))) {	// user not found
            $this->error='Email address not registered!';
            $this->view_login_form();
        }
        if($this->process_recovery($user)){
            $data['reset_email_confirm']=true;
            $data['form_error']='';
            echo $this->CI->load->view('login/index', $data,true);
            $this->end_now();
        }
        $this->view_login_form();
    }

    /**
     * validate and process the password recovery
     * @access private
     * @return bool
     */
    private function process_recovery($user){
        $key=$this->CI->ezuser->requestPassword($user->id);
        $data['url']=$this->CI->ezuri->RbacUrl("resetpassword/key/$key/e/".rawurlencode($user->email));
        $email_body=$this->CI->load->view('login/_password_email', $data,true);

        //Disable this while you running the script on server
        die($email_body);

        $config['mailtype']='html';
        $config['wordwrap'] = FALSE;
        $this->CI->email->initialize($config);
        $this->CI->email->from($this->CI->config->item('password_recovery_email', 'ez_rbac'), 'EzRbac');
        $this->CI->email->to($user->email);
        $this->CI->email->subject($this->CI->config->item('password_recovery_subject', 'ez_rbac'));
        $this->CI->email->message($email_body);
        $this->CI->email->set_alt_message('View the mail using a html email client');
        $this->CI->email->send();

        return TRUE;
    }


    /**
     * validate and reset the password
     * @access public
     * @param array $param
     * @return boolean
     */
    public function resetPassword($param=array()){
        $key=$param['key'];
        $email=rawurldecode($param['e']);
        $this->CI->load->model('ezuser');
        $user = $this->CI->ezuser->get_user_by_email($email);

        if($user==null){
            show_404();
        }

        if($this->validateRequestHash($user,$key)){

            if(isset($_POST['ResetForm'])){
                $this->CI->form_validation->set_rules($this->validation_rule(2));
                if ($this->CI->form_validation->run() == FALSE){
                    $this->error=validation_errors();
                    $this->view_password_reset_form();
                }

                $this->CI->load->model('ezuser');
                $password=$this->CI->input->post("password",TRUE);
                $this->CI->ezuser->set_new_password((string)$password);
                $this->view_password_reset_message();
            }
            $this->view_password_reset_form();
        }else{
            show_404();
        }
    }

    /**
     * @param $user
     * @param string $key
     * @return bool
     */
    private function validateRequestHash($user,$key=""){
        if($key==""){
            return false;
        }
        $date = new DateTime($user->reset_request_time);
        $date->modify("+2 day");
        return (md5($user->reset_request_code.$user->reset_request_time.$user->reset_request_ip)===$key && $date>new DateTime());
    }

    /**
     * trrminate the execution within the script! we will be stop here and
     * further execution will be stoped
     * I have not found anything to detect the exit , so doing it manually!!
     * Hope the pice of code will not be necessary when i figure it out!!!
     */
    private function end_now(){
        $this->CI->we_are_done=true;
        exit;
    }

}
/* End of file ezlogin.php */
/* Location: ./ezRbac/libraries/ezlogin.php */