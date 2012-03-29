<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Ezuser
 *
 * This model represents user access mapping data. It can be used
 * for manipulation and retriving access previlages information.
 *
 * @version	1.0
 * @package ezRbac
 * @since ezRbac v 0.2
 * @author Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright Copyright &copy; 2012 Roni Saha
 * @license	GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class user_access_map extends  CI_Model {

    private $CI;

    private $_table_name;


	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->CI=& get_instance();

        $this->_table_name=$this->CI->config->item('access_map_table','ez_rbac');
    }

    /**
     * Get permission info by access_role and controller
     *
     * @param string $access_role
     * @param string $controller
     * @return    object
     */
    function get_permission($access_role,$controller="")
    {
        //Default return value
        $ret=array();
        $this->db->select('permission,controller');
        $this->db->where('user_role_id',$access_role);
        if($controller!=""){
            $this->db->where('controller',$controller);
            $ret =NULL;
        }

        $query = $this->db->get($this->_table_name);
        if ($query->num_rows() > 0) {
            if($controller==""){    //Get all permission info for selected user
                $output=array();
                foreach ($query->result() as $prow) {
                    $output[$prow->controller]=$prow->permission;
                }
                return $output;
            }
            $row=$query->row();
            return $row->permission;
        }
        return $ret;
    }





}


/* End of file user_access_map.php */
/* Location: ./ezRbac/models/user_access_map.php */