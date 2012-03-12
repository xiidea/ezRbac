<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * AccessMap class file. 
 * A Simple Access Control Mapping Library
 *
 * @version	1.0
 * @package ezRbac
 * @since ezRbac v 0.1
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
	 $access_arr=array("publish","delete","edit","create","view"),

    /**
     * @var int<p>
     * Used to store the length of access string by auto detecting the access_arr size
     * </p>
     */
    $access_map_array_size=5,
    /**
     * @var string <p>
     * if a controller not found in the access table what should be the default result
     * </p>
     */
    $default_access="00001",

    /**
	 * @var Array sets the access values after parsing the access string
	 */
	$access_val=array(),
	
	/**
	 * @var mix cache the access details value in array after 1st time parsing
	 */
	$access_details=false;


    /**
     * @access Public
     * @param $param paremeter array get the controller name
     */
    function __construct($param){
        $this->CI = & get_instance();
		$controller=strtolower($param["controller"]);

        //Try to Auto Login
        $this->auto_login();

        if($this->isGuest()){   //Nothing to do but handle login
            $action= $this->CI->input->post("action")?:'login';
            $this->CI->load->library('ezlogin_lib');
            if(!($action=='login' || $action=='recover_password')){ //This usefull for wrong action parameter
                $action='login';
            }
            $this->CI->ezlogin_lib->$action();
        }

        $this->initialize($controller);

	}

    /**
     * Initialize the access checking variables
     * @access private
     * @param $controller
     */
    private function initialize($controller){
        $default_access_map=$this->CI->config->item('default_access_map', 'ez_rbac');
        if($default_access_map){
            if(is_array($default_access_map)&& !empty($default_access_map))
            $this->access_arr=$this->CI->config->item('default_access_map', 'ez_rbac');
        }

        //Make all the valu lower case
        $this->access_arr=array_map('strtolower',$this->access_arr);

        $this->access_map_array_size=count($this->access_arr);

        $access_str=$this->get_access_string($controller);
        $access_val=$this->validate($access_str);
        $this->access_val=str_split($access_val);
    }

    /**
     * Automatic login user form cookie value return false if no valid cookie information found and auto login faild
     * @access private
     * @return bool
     */
    private function auto_login(){
        if ($this->isGuest()) {			// not logged in (as any user)
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
        return FALSE;
    }

    /**
     * return the access_map array
     * @access Public
     * @return array
     */
    function get_access_map(){
        return $this->access_arr;
    }

    /**
     * check if the access is from any loged in user or not by checking the existance of session data
     * @access Public
     * @return bool
     */
    function isGuest(){
        return (!$this->CI->session->userdata('user_id'));
    }

	/**
	 * Return Binary string in a acceptable format
	 * @access private 
	 * @param String Controller Name
	 * @return String. Binary string in a acceptable format
	 */	
	private function get_access_string($controller){

		if(! $this->CI->session->userdata('access_role'))
		{
			return FALSE;
		}
		$access_role = $this->CI->session->userdata('access_role');

        $this->CI->db->select('permission');
        $this->CI->db->where('user_role_id',$access_role);
        $this->CI->db->where('controller',$controller);
		$user_access=$this->CI->db->get('user_access_map');
		$row=$user_access->result();
        if(!$row){
            return $this->default_access;
        }
		return $this->validate($row[0]->permission);
	}
	
	/**
	 * Get all access mode array
	 * @access public
	 * @param none 
	 * @return array of access mode.
	 */
	public function getAccessDetails(){
		if($this->access_details){
			return $this->access_details;
		}
		$access_details=array();
		foreach ($this->access_arr as $key=>$val){
			$access_details[$val]=(boolean)$this->access_val[$key];
		}
		$this->access_details=$access_details;
		return $access_details;
	}
	
	/**
	 * Return Binary string in a acceptable format
	 * @access private
	 * @param String
	 * @return String. Binary string in a acceptable format
	 */	
	private function validate($access_str){
		return str_pad($access_str,$this->access_map_array_size,0) & str_repeat('1',$this->access_map_array_size);
	}

	/**
	 * Magic function to handle the access mode dynamically
	 * @param String<p> 
	 * The method name that called
	 * </p>
	 * @param Array of arguments
	 * @return Boolean <p>Return access mode or false on any undefined function call</p>
	 */
	function __call($method,$arguments) {
		$verb=strtolower(substr($method, 0, 3));
		$access_details=$this->getAccessDetails();
		if($verb=="can"){
			$checkval=strtolower(substr($method, 3));
			return array_key_exists($checkval,$access_details) ? $access_details[$checkval] : false;
		}
		return false;
	}
}
/*************************************************
 * Stand alone uses example
 *
 * $a=new AccessMap(array("controller"=>get_class($this)));
 * $b=$a->getAccessDetails();
 *
 * if($b['edit']){
 *   echo "you can edit";
 * }
 *
 * if($b['delete']){
 *  echo "you can delete";
 * }
 *
 * or
 *
 * if($b->canPublish(false)){
 *  echo "you can publish";
 * }
 *
 * or only
 * $b->canPublish() //Redirect to $redirect_path if have no publish previlage
 */


/* End of file accessmap.php */
/* Location: ./ezRbac/libraries/accessmap.php */