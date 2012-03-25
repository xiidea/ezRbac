<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ezuri class
 * A simple class to create and parse package specifiq requests
 *
 * @version	1.0
 * @package ezRbac
 * @since ezRbac v 0.2
 * @author Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright Copyright &copy; 2012 Roni Saha
 * @license	GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class ezuri
{
    /**
     * @var CI_Controller CI instance reference holder
     */
    private $CI;


    /**
     * @var string rbac specific url identifier
     */
    private $_manage_url;

    /**
     * @var bool true if the routing used for clean url
     */
    private $_use_routing=false;

    private $_base_url;

    private $_rbac_param=array();

    function __construct()
    {
        $this->CI = & get_instance();
        $this->_manage_url=$this->CI->config->item('ezrbac_url','ez_rbac');
        $this->_use_routing=$this->CI->config->item('use_routing','ez_rbac');
        $this->_base_url=$this->_use_routing?$this->_manage_url:$this->CI->router->default_controller."/index/$this->_manage_url";
    }

    public function isRbacUrl(){
        $the_key=strtolower($this->CI->uri->rsegment(3));
        if($the_key==strtolower($this->_manage_url)){
            $this->_rbac_param= $this->CI->uri->ruri_to_assoc(5);
            return $this->CI->uri->rsegment(4);
        }
        return FALSE;
    }

    public function RbacParam(){
        if(empty($this->_rbac_param)){  //Assuming the is Rbac never been called so call it first
            $this->isRbacUrl();
        }
        return $this->_rbac_param;
    }

    public function logout(){
        return $this->_base_url."/logout";
    }

    public function RbacUrl($uri=""){
        return site_url($this->_base_url."/$uri");
    }

    public function ruri_string($separator="/"){
        return  join($separator,array_slice( $this->CI->uri->rsegment_array(), 3));
    }

    public function assets_url($uri=""){
        if($uri=="")
            return false;

        if($this->CI->config->item('use_assets_within_package','ez_rbac')){
            return base_url($this->_base_url."/assets/".$uri);
        }
        return base_url($this->CI->config->item('assets_base_directory','ez_rbac')."/$uri");
    }
}
/* End of file ezlogin.php */
/* Location: ./ezRbac/libraries/ezlogin.php */