<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * AccessMap class file. 
 * A Simple Access Control Mapping Library
 *
 * @version	1.0
 * @package ezRbac
 * @since ezRbac v 0.3
 * @author Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright Copyright &copy; 2012 Roni Saha
 * @license	GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 */
class AccessMap{

 private

     /**
      * @var CI_Controller CI instance reference holder
      */
     $CI,
     /**
      * @var Array sets the default action list
      */
     $_access_arr = array("view", "create", "edit", "delete", "publish"),

    /**
     * @var int<p>
     * Used to store the length of access string by auto detecting the access_arr size
     * </p>
     */
    $_access_map_array_size=5,
    /**
     * @var Integer <p>
     * if a controller not found in the access table what should be the default result
     * </p>
     */
    $_default_access= 1,

     /**
      * @var Integer sets the access values after parsing the access string
      */
     $_access_val = 0,

     /**
      * @var array cache the access details value in array after 1st time parsing
      */
     $_access_details = array(),

     $_login_session_key;


    /**
     * @access Public
     *
     * @param array $param array get the controller name
     */
    function __construct($param = array())
    {
        $this->CI = & get_instance();

        $controller = (isset($param["controller"])) ? strtolower($param["controller"]) : FALSE;
        $this->_login_session_key = $this->CI->config->item('login_session_key', 'ez_rbac');

        $default_access_map = $this->CI->config->item('default_access_map', 'ez_rbac');
        if (is_array($default_access_map) && !empty($default_access_map)) {
            $this->_access_arr = $this->CI->config->item('default_access_map', 'ez_rbac');
        }
        //Make all the value lower case
        $this->_access_arr = array_map('strtolower', $this->_access_arr);

        if ($controller) {
            if ($this->isGuest()) { //Nothing to do but handle login
                $action = $this->CI->input->post("action") ? $this->CI->input->post("action") : 'login';
                if (!($action == 'login' || $action == 'recover_password')) { //This useful for wrong action parameter
                    $action = 'login';
                }

                $this->CI->ezlogin->$action();
            }
            $this->initialize($controller);
        }
    }

    /**
     * Initialize the access checking variables
     *
     * @access public
     *
     * @param      $controller
     * @param bool $access_role
     */
    public function initialize($controller, $access_role = FALSE)
    {

        $this->_access_val = $this->get_permission($controller, $access_role);
        $this->_access_map_array_size = count($this->_access_arr);
    }

    /**
     * return the access_map array
     *
     * @access Public
     * @return array
     */
    function get_access_map()
    {
        return $this->_access_arr;
    }

    /**
     * return the _access_val
     *
     * @access Public
     * @return integer
     */
    function get_access_str()
    {
        return $this->_access_val;
    }

    /**
     * check if the access is from any loged in user or not by checking the existance of session data
     *
     * @access Public
     * @return bool
     */
    function isGuest()
    {
        if (!$this->CI->session->userdata($this->_login_session_key)) {
            //try to auto login first
            $this->CI->load->library('ezlogin');
            return !$this->CI->ezlogin->auto_login();
        }
        return FALSE;
    }

    /**
     * Return User permission value
     *
     * @access private
     *
     * @param              String Controller Name
     * @param bool|integer $access_role
     *
     * @return Integer. access permission of loggedin user
     */
    private function get_permission($controller, $access_role = FALSE)
    {
        if (!$access_role && !$this->CI->session->userdata($this->_login_session_key)) {
            echo "should not be here";
            return FALSE;
        }

        if (!$access_role) {
            $access_role = $this->CI->session->userdata('access_role');
        }

        $this->CI->load->model('user_access_map');
        $permission = $this->CI->user_access_map->get_permission($access_role, $controller);

        if (is_null($permission)) {
            return $this->_default_access;
        }

        return (int)$permission;
    }

    /**
     * Get all access mode array
     *
     * @access public
     *
     * @param none
     *
     * @return array of access mode.
     */
    public function getAccessDetails()
    {
        if ($this->_access_details) {
            return $this->_access_details;
        }
        $access_details = array();
        foreach ($this->_access_arr as $key => $val) {
            $access_details[$val] = (boolean)$this->_access_val[$key];
        }
        $this->_access_details = $access_details;
        return $access_details;
    }
    /**
     * Return Binary string in a acceptable format
     * @access public
     * @param String
     * @return String. Binary string in a acceptable format
     */
    public function validate($access_str){
        return str_pad($access_str,$this->_access_map_array_size,0,STR_PAD_LEFT) & str_repeat('1',$this->_access_map_array_size);
    }


    public function can($action)
    {

        if (isset($this->_access_details[$action])) {
            return $this->_access_details[$action];
        }

        $index = array_search($action, $this->_access_arr);

        if ($index === FALSE) {
            return $this->_access_details[$action] = FALSE;
        }

        return $this->_access_details[$action] = (boolean)($this->_access_val & pow(2, $index));

    }

    /**
     * Magic function to handle the access mode dynamically
     *
     * @param String<p>
     *              The method name that called
     * </p>
     * @param Array of arguments
     *
     * @return Boolean <p>Return access mode or false on any undefined function call</p>
     */
    function __call($method, $arguments)
    {
        $verb = strtolower(substr($method, 0, 3));
        if ($verb == "can") {
            $action = strtolower(substr($method, 3));
            return $this->can($action);
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined function : ' . $method .
                ' called in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return NULL;
    }
}

/* End of file accessmap.php */
/* Location: ./ezRbac/libraries/accessmap.php */