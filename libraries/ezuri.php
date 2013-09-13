<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ezuri class
 * A simple class to create and parse package specifiq requests
 *
 * @version    1.0
 * @package    ezRbac
 * @since      ezRbac v 0.2
 * @author     Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright  Copyright &copy; 2012 Roni Saha
 * @license    GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
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
    private $_use_routing = FALSE;

    /**
     * @var string Cache base url value for ezrbac specific url
     */
    private $_base_url;

    /**
     * @var array cache the request parameters as array
     */
    private $_rbac_param = array();

    /**
     * Constructor function
     */
    function __construct()
    {
        $this->CI           = & get_instance();
        $this->_manage_url  = $this->CI->config->item('ezrbac_url', 'ez_rbac');
        $this->_use_routing = $this->CI->config->item('use_routing', 'ez_rbac');
        $this->_base_url    = $this->_use_routing ? $this->_manage_url : $this->CI->router->default_controller . "/index/$this->_manage_url";
    }

    /**
     * Check if current url is a Rbac Specific url or not
     *
     * @return bool
     */
    public function isRbacUrl()
    {
        $the_key = strtolower($this->CI->uri->rsegment(3));
        if ($the_key == strtolower($this->_manage_url)) {
            $this->_rbac_param = $this->CI->uri->ruri_to_assoc(5);
            return $this->CI->uri->rsegment(4);
        }
        return FALSE;
    }


    /**
     * Return the $_rbac_param array
     *
     * @return array
     */
    public function RbacParam()
    {
        if (empty($this->_rbac_param)) { //Assuming the is Rbac never been called so call it first
            $this->isRbacUrl();
        }
        return $this->_rbac_param;
    }


    /**
     * return the logout url
     *
     * @return string
     */
    public function logout()
    {
        return $this->_base_url . "/logout";
    }

    /**
     * Create/Convert a uri to rbac specific uri
     *
     * @param string $uri
     *
     * @return string
     */
    public function RbacUri($uri = "")
    {
        return $this->_base_url . "/$uri";
    }

    /**
     * Create/Convert a uri to rbac specific URL
     *
     * @param string $uri
     *
     * @return mixed
     */
    public function RbacUrl($uri = "")
    {
        return site_url($this->RbacUri($uri));
    }

    /**
     * Return the rbac specific segment array after all routing
     *
     * @param int $n
     *
     * @return array
     */
    public function rsegment_array($n = 0)
    {
        return array_slice($this->CI->uri->rsegment_array(), 3 + $n);
    }

    /**
     * get rbac specific uri string after routing done!
     *
     * @param string $separator
     *
     * @return string
     */
    public function ruri_string($separator = "/")
    {
        return join($separator, $this->rsegment_array());
    }

    /**
     * return the assets access url direct/with media library!
     *
     * @param string $uri
     *
     * @return bool
     */
    public function assets_url($uri = "")
    {
        if ($uri == "")
            return FALSE;

        if ($this->CI->config->item('use_assets_within_package', 'ez_rbac')) {
            return site_url($this->_base_url . "/assets/" . $uri);
        }
        return base_url($this->CI->config->item('assets_base_directory', 'ez_rbac') . "/$uri");
    }
}
/* End of file ezlogin.php */
/* Location: ./ezRbac/libraries/ezlogin.php */