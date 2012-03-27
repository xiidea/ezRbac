<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ezmanage class
 * A simple class to manage ACL through a easy GUI
 *
 * @version	1.0
 * @package ezRbac
 * @since ezRbac v 0.2
 * @author Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright Copyright &copy; 2012 Roni Saha
 * @license	GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class ezmanage
{
    /**
     * @var CI_Controller CI instance reference holder
     */
    private $CI;

    private $valid_action=array('login','logout','acl');


    function __construct($param)
    {
       $this->CI = & get_instance();
       if(empty($param)){
           $this->login();
           return;
       }
       if(!in_array($param[0],$this->valid_action)){
           show_404();
       }
       $this->$param[0]();
    }

    /**
     * handle login
     * @access Public
     */
    private function login(){
        if($this->isLogedin()){
            redirect($this->uri('acl'));
        }

        $this->CI->form_validation->set_rules('password', 'Password', 'required|xss');
        if ($this->CI->form_validation->run() == FALSE){
            $data['form_error'] = validation_errors();
            echo $this->CI->load->view('manage/login', $data,true);
            return;
        }

        if($this->process_login()){
            redirect($this->uri('acl'));
        }

        echo $this->CI->load->view('manage/login', array('form_error'=>'incorrect password! try again'),true);
        return;
    }

    private function process_login(){
        if($this->CI->config->item('ezrbac_password', 'ez_rbac')==$this->CI->input->post("password",TRUE)){
            $this->CI->session->set_userdata('rbac_gui_logedin', true);
            return true;
        }
        return false;
    }

    private function logout(){
        $this->CI->session->set_userdata('rbac_gui_logedin', false);
        redirect($this->uri());
    }

    private function isLogedin(){
        return  $this->CI->session->userdata('rbac_gui_logedin');
    }

    private function uri($uri=""){
       return $this->CI->ezuri->RbacUri($this->CI->config->item('ezrbac_gui_url', 'ez_rbac')."/$uri");
    }

    private function url($uri=""){
        return site_url($this->uri($uri));
    }


    private function acl(){
        if(!$this->isLogedin()){
            redirect($this->uri('login'));
        }
        echo "access control list <br />";
        echo anchor($this->uri('logout'),'Logout');
    }


}
