<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ezRbacHook class file.
 * A Simple Codeigniter Hook To handle all controller request
 *
 * @version    1.2
 * @package    ezRbac
 * @since      ezRbac v 0.3
 * @author     Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright  Copyright &copy; 2012 Roni Saha
 * @license    GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class EzRbacHook
{
    private
        /**
         * @var CI_Controller CI instance reference holder
         */
        $CI,

        /**
         * @var array
         */
        $_custom_access_map = array(),
        /**
         * @var array
         */
        $_public_controller = array(),
        /**
         * @var
         */
        $_controller,
        /**
         * @var
         */
        $_loginUrl = "",
        /**
         * @var
         */
        $_controller_name,
        /**
         * @var bool
         */
        $_isAjaxCall = FALSE;


    /**
     *Constructor to initial all data and libraries
     */
    function __construct()
    {
        //Get the Codeigniter instance
        $this->CI = & get_instance();

        //Load al required core library
        $this->load_libraries();

        //Make a public variable to controller so we can access it from any where within script execution!
        $this->CI->ezRbacPath = dirname(__FILE__);

        //Load configurations
        $this->loadConfiguration();


        //Get list of public controller from the config file
        $this->_public_controller = $this->CI->config->item('public_controller', 'ez_rbac') ?
            $this->CI->config->item('public_controller', 'ez_rbac') :
            array();

        //Get the login url
        $this->_loginUrl = $this->CI->config->item('login_url', 'ez_rbac');
        //Load the own uri library
        $this->CI->load->library('ezuri');


        $this->CI->load->library('ezrbac');
    }

    /**
     * Load ezrbac configuration, the configuration can now put in application/config/ez_rbac.php also
     * This way if user update this library the changed configuration can stay unchaged
     */
    private function loadConfiguration()
    {
        $library_directory = basename($this->CI->ezRbacPath);

        //We should use our package resource!
        $this->CI->load->add_package_path(APPPATH . "third_party/$library_directory/");
        $this->CI->config->load('ez_rbac', TRUE, TRUE);
        $this->CI->load->remove_package_path(APPPATH . "third_party/$library_directory/");
        $this->CI->config->load('ez_rbac', TRUE, TRUE);
        $this->CI->load->add_package_path(APPPATH . "third_party/$library_directory/");
    }

    /**
     * Check if the request belongs to a public resource or not
     *
     * @return bool
     */
    private function isPublicRequest()
    {
        $this->_controller_name = $this->CI->router->fetch_class();
        $this->_controller      = $this->CI->router->fetch_directory() . $this->_controller_name;

        //I am sure login controller is a public resource
        if ($this->CI->uri->ruri_string() == $this->_loginUrl || in_array($this->_controller, $this->_public_controller)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * This method will handle all library specific url like namege rbac or logout
     *
     * @return bool
     */
    private function manage_access()
    {
        $isRbacUrl = $this->CI->ezuri->isRbacUrl();
        switch ($isRbacUrl) {
            case FALSE:
                //Its not a rbac url so nothing to do or worry about
                return TRUE;
            case 'resetpassword':
                $this->CI->load->library('ezlogin');
                $this->CI->ezlogin->resetPassword($this->CI->ezuri->RbacParam());
                break;
            case 'logout':
                $this->CI->load->library('ezlogin');
                $this->CI->ezlogin->logout();
                redirect($this->CI->router->default_controller);
                break;
            case 'assets':
                $this->CI->load->library('ezmedia');
                break;
            default:
                if ($this->CI->config->item('ezrbac_gui_url', 'ez_rbac') == $isRbacUrl) { //Is the url for manageing guii
                    //check if the setting allows us to access this or not
                    if ($this->CI->config->item('enable_ezrbac_gui', 'ez_rbac')) {
                        //So we are all set to go!!
                        $this->CI->load->library('ezmanage', $this->CI->ezuri->rsegment_array(1));
                        $this->end_now();
                    }

                }
                //its a invalid rbac request show error
                show_404();
                break;
        }
    }

    /**
     * The access Checking function
     *
     * @return bool
     */
    function accessCheck()
    {

        $this->manage_access();

        //if The requested controller in a public domain so give access permission no need to go further
        if ($this->isPublicRequest()) {
            return null;
        }

        //If we do not have to handle login then its better check this now!!
        if ($this->_loginUrl != "" && !$this->CI->session->userdata($this->CI->config->item('login_session_key', 'ez_rbac'))) {
            //user not logged in and you wished to handle it your self. Here you go
            redirect($this->_loginUrl);
        }


        $controller_methods = get_class_methods($this->_controller_name);
        $method=$this->CI->router->fetch_method();

        //Called method does not exist!
        if(!in_array($method, $controller_methods)){
            return null;
        }

        //Get custom access map defined in controller
        if(in_array('access_map', $controller_methods)){
            $this->_custom_access_map=$this->CI->access_map();
        }

        $this->CI->load->library('accessmap', array("controller" => $this->_controller));

        //Check if the request is ajax or not
        $this->_isAjaxCall = ($this->CI->input->get('ajax') || $this->CI->input->is_ajax_request());

        $access_map = $this->CI->accessmap->get_access_map();
        if (!in_array($method, $access_map)) { //The method is not in default acess map
            if (!isset($this->_custom_access_map[$method])) { //The method is not defined in custom access map
                if ($this->CI->config->item('default_access', 'ez_rbac')) {
                    return TRUE; //Default access for action is set to true
                }
                $this->restrict_access();
            }
            $method = $this->_custom_access_map[$method];
        }

        $method = "can" . Ucfirst($method);

        if (!$this->CI->accessmap->$method()) { //We do not have the access permission!
            $this->restrict_access();
        }

    }

    /**
     * This method trigger when a restricted resource accessed
     */
    private function restrict_access()
    {
        if ($this->_isAjaxCall) { //do not redirect return json object if it is a ajax request
            $msg = json_encode(array('success' => FALSE, 'msg' => $this->CI->config->item('ajax_no_permission_msg', 'ez_rbac')));
            set_status_header(403, "forbidden");
            die($msg);
        }
        if ($this->CI->config->item('redirect_url', 'ez_rbac')) {
            redirect($this->CI->config->item('redirect_url', 'ez_rbac'));
        }
        show_error('you do not have sufficient permission to access this resource', 403);
    }

    /**
     * Load all system libraries and helpers needed by the library
     */
    private function load_libraries()
    {
        $this->CI->load->helper(array('cookie', 'url', 'file'));
        $this->CI->load->database();
        $this->CI->load->library(array('session', 'sha1', 'encrypt', 'form_validation'));
    }

    /**
     * terminate the execution within the script! we will be stop here and
     * further execution will be stopped
     * I have not found anything to detect the exit , so doing it manually!!
     * Hope the pice of code will not be necessary when i figure it out!!!
     */
    private function end_now()
    {
        $this->CI->we_are_done = TRUE;
        exit;
    }

    /**
     * Doing all cleanup stuff
     * We haven't forgot to remove the package path after finishing everything!.
     * We do not need to load third party resources anymore!
     */
    function __destruct()
    {
        $library_directory = basename($this->CI->ezRbacPath);
        $this->CI->load->remove_package_path(APPPATH . 'third_party/'.$library_directory.'/');
        if (isset($this->CI->we_are_done)) {
            //The script life time ends here!! We should ensure to end the output object by displaying
            //or sending headers we set earlier!!
            $this->CI->output->_display();
            if (class_exists('CI_DB') && isset($this->CI->db)) {
                $this->CI->db->close();
            }
            $this->CI = NULL;
        }
    }
}


/* End of file ezRbacHook.php */
/* Location: ./ezRbac/ezRbacHook.php */