<?php
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
     * @var array list of rbac specific urls
     */
    private $_manage_urls=array('rbac','logout' );

    private $_rbac_param=array();

    function __construct()
    {
        $this->CI = & get_instance();
    }

    public function RbacUrl(){
        $n=$this->CI->router->fetch_directory()?4:3;
        $the_key=strtolower($this->CI->uri->segment($n));
        if(in_array($the_key, array_map('strtolower', $this->_manage_urls))){
            $this->_rbac_param= $this->CI->uri->uri_to_assoc($n+1);
            return $the_key;
        }
        return FALSE;
    }

    public function RbacParam(){
        return $this->_rbac_param;
    }

    public function logout(){
        return $this->CI->router->default_controller."/index/logout";
    }

}
/* End of file ezlogin.php */
/* Location: ./ezRbac/libraries/ezlogin.php */