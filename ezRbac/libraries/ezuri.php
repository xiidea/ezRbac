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

    function __construct()
    {
        $this->CI = & get_instance();
    }

    public function logout(){
        return $this->CI->router->default_controller."/index/logout";
    }

}
/* End of file ezlogin.php */
/* Location: ./ezRbac/libraries/ezlogin.php */