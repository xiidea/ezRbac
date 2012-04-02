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

    private $_valid_action=array('login','logout','acl');

    private $_request_params=array();


    function __construct($param)
    {
       $this->CI = & get_instance();
       if(empty($param)){
           $this->login();
           return;
       }
       if(!in_array($param[0],$this->_valid_action)){
           show_404();
       }
       $this->_request_params=array_splice($param,1);
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

    public function uri($uri=""){
       return $this->CI->ezuri->RbacUri($this->CI->config->item('ezrbac_gui_url', 'ez_rbac')."/$uri");
    }

    public function url($uri=""){
        return site_url($this->uri($uri));
    }

    private function acl_ajax($param){
        switch ($param[0]){
            case 'get_permission':
                $this->CI->accessmap->initialize($this->CI->input->post('controller'),$this->CI->input->post('user_role_id'));
                echo json_encode($this->CI->accessmap->get_access_str());
                break;
            case 'update':
                $this->CI->load->model('user_access_map');
                $p= $this->CI->accessmap->validate(decbin(array_sum($_POST['permission'])));
                $this->CI->user_access_map->set_permission($this->CI->input->post('controller'),$this->CI->input->post('user_role_id'),$p);
                echo "ok";
                break;
            default:
                //nothing to do
                return;
        }
    }

    private function acl(){
        if(!$this->isLogedin()){
            redirect($this->uri('login'));
        }

        //After this level we onlly allowed ajax call!!
        if(!empty($this->_request_params) && !$this->CI->input->is_ajax_request()){
            show_404();
        }

        $this->CI->load->library('accessmap');

        //Handle ajax request
        if(!empty($this->_request_params)){
            $this->acl_ajax($this->_request_params);
            return;
        }

        //First time came here!! Visit our interface!!!
        $this->CI->load->model('manage/ezcontrollers');
        $this->CI->load->model('user_access_map');
        //Get all controller list except public controllers
        $clist=array_diff($this->CI->ezcontrollers->get_controllers(),$this->CI->config->item('public_controller', 'ez_rbac'));

       // $amap_from_db=$this->CI->user_access_map->get_permission(1);

      //  $this->dump_me($clist,$amap_from_db,$this->_request_params);



        $data=array(
            'acl_url'=>$this->url(''),
            'controller_list'=>$clist,
            'access_roles'=>$this->CI->user_access_map->get_role_list(),
            'access_list'=>$this->CI->accessmap->get_access_map()
        );
        echo $this->CI->load->view('manage/acl', $data,true);
        //echo "access control list <br />";
       // echo anchor($this->uri('logout'),'Logout');
    }

    private function dump_me(){
        echo "<pre>";
        var_dump(func_get_args());
        echo "</pre>";

    }

}
