<?php
/**
 * ezlogin_lib class
 * A simple class adds the login feature to the package
 *
 * @version	1.0
 * @package ezRbac
 * @since ezRbac v 0.1
 * @author Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright Copyright &copy; 2012 Roni Saha
 * @license	GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class ezlogin_lib
{
    /**
     * @var CI_Controller CI instance reference holder
     */
    private $CI;

    function __construct()
    {
        $this->CI = & get_instance();
    }

    /**
     * @return array The validation rule for login form
     */
    private function validation_rule(){
        return array(
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
        );
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
        exit;
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

        if (!is_null($user = $this->CI->ezuser->get_user_by_email($useemail))) {	// login ok

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
     * Logout user from the site
     *
     * @return	void
     */
    function logout()
    {
        $this->delete_autologin();

        // See http://codeigniter.com/forums/viewreply/662369/ as the reason for the next line
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
     *@TODO Implement the recover password feature
     */
    function recover_password(){
        die("recover_password: feature is comming soon");
    }
}
/* End of file ezlogin_lib.php */
/* Location: ./ezRbac/libraries/ezlogin_lib.php */