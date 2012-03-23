<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ezRbacHook class file.
 * A Simple Codeigniter Hook To handle all controller request
 *
 * @version	1.1
 * @package ezRbac
 * @since ezRbac v 0.2
 * @author Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright Copyright &copy; 2012 Roni Saha
 * @license	GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 * 
 */
class ezRbacHook{
private
    /**
     * @var CI_Controller CI instance reference holder
     */
	 $CI,

    /**
     * @var array
     */
    $custom_access_map=array( ),
    /**
     * @var array
     */
    $public_controller=array(),
    /**
     * @var
     */
    $controller,
    /**
     * @var
     */
    $controller_name,
    /**
     * @var bool
     */
    $isAjaxCall=false;

    /**
     *Constructor to initial all data and libraries
     */
    function __construct(){
        //Get the Codeigniter instance
        $this->CI = & get_instance();

        //Load al required core library
        $this->load_libraries();

        //We should use our package resource!
        $this->CI->load->add_package_path(APPPATH.'third_party/ezRbac/');
        $this->CI->config->load('ez_rbac', TRUE, TRUE);

        //Get list of public controller from the config file
        $this->public_controller=$this->CI->config->item('public_controller', 'ez_rbac')?
                                 $this->CI->config->item('public_controller', 'ez_rbac'):
                                 array();

        //Load the own uri library
        $this->CI->load->library('ezuri');
	}

    /**
     * Check if the request belongs to a public resource or not
     * @return bool
     */
    private function isPublicRequest(){
        $this->controller_name=$this->CI->router->fetch_class();
        $this->controller=$this->CI->router->fetch_directory().$this->controller_name;

        if(in_array($this->controller,$this->public_controller)){
            return true;
        }
        return false;
    }

    /**
     * This method will handle all library specific url like namege rbac or logout
     */
    private function manage_access(){
       // echo $this->CI->router->default_controller;
        $n=$this->CI->router->fetch_directory()?4:3;
        switch($this->CI->uri->segment($n)){
            case 'rbac':
               // $uriparam= $this->CI->uri->uri_to_assoc($n+1);
              //@TODO Implement Access Controll management library
                echo "manage my access control system";
                exit;
                break;
           case 'logout':
               $this->CI->load->library('ezlogin');
               $this->CI->ezlogin->logout();
               redirect($this->CI->router->default_controller);
               break;
        }
    }

    /**
     * The access Checking function
     * @return bool
     */
    function AccessCheck(){

        $this->manage_access();

        //if The requested controller in a public domain so give access permission no need to go further
        if($this->isPublicRequest()){
            return;
        }
        //Get custom access map defined in controller
        if(in_array(strtolower('access_map'), array_map('strtolower', get_class_methods($this->controller_name)))){
            $this->custom_access_map=$this->CI->access_map();
        }

        $this->CI->load->library('accessmap', array("controller"=>$this->controller));

        $method=$this->CI->router->fetch_method();

        //Check if the request is ajax or not
        $this->isAjaxCall=($this->CI->input->get('ajax')|| $this->CI->input->is_ajax_request());

        $access_map=$this->CI->accessmap->get_access_map();
        if(!in_array($method,$access_map)){    //The method is not in default acess map
            if(!isset($this->custom_access_map[$method])){   //The method is not defined in custom access map
                if($this->CI->config->item('default_access', 'ez_rbac')){
                   return true;     //Default access for action is set to true
                }
               $this->restrict_access();
            }
            $method=$this->custom_access_map[$method];
        }

        $method="can".Ucfirst($method);

        if(!$this->CI->accessmap->$method()){   //We do not have the access permission!
            $this->restrict_access();
        }

    }

    /**
     * This method trigger when a restricted resource accessed
     */
    private function restrict_access(){
        if($this->isAjaxCall){   //do not redirect return json object if it is a ajax request
            die(json_encode(array('success'=>false,'msg'=>$this->CI->config->item('ajax_no_permission_msg', 'ez_rbac'))));
        }
        if($this->CI->config->item('redirect_url', 'ez_rbac')){
            redirect($this->CI->config->item('redirect_url', 'ez_rbac'));
        }
        show_error('you do not have sufficient permission to access this resource',403);
    }

    /**
     * Load all system libraries and helpers needed by the library
     */
    private function load_libraries(){
        $this->CI->load->helper('cookie');
        $this->CI->load->database();
        $this->CI->load->library(array('session','sha1','encrypt','form_validation'));
    }

    /**
     * Dont forget to remove the package path after finishing everything.
     * We do not need to load thirdparty resources anymore!
     */
    function __destruct(){
        $this->CI->load->remove_package_path(APPPATH.'third_party/ezRbac/');
    }
}


/* End of file ezRbacHook.php */
/* Location: ./ezRbac/ezRbacHook.php */