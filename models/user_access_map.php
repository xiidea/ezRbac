<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * user_access_map model class
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
    /**
     * @var CI_Controller CI instance reference holder
     */
    private $CI;

    /**
     * @var String $_table_name store access_map_table name
     */
    private $_table_name;

    /**
     * @var String $_user_role_table store user_role_table name
     */
    private $_user_role_table;


    /**
     * Constructor Function
     */
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->CI=& get_instance();

        $this->_table_name=$this->CI->config->item('access_map_table','ez_rbac');
        $this->_user_role_table=$this->CI->config->item('user_role_table','ez_rbac');
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

    /**
     * Save access permission to database
     * @param $controller
     * @param $role
     * @param $permission
     * @return mixed
     */
    function set_permission($controller,$role,$permission){
        $data['user_role_id']=$role;
        $data['controller']=$controller;
        $data['permission']=$permission;
        $where=array('user_role_id'=>$role,'controller'=>$controller);
        $query = $this->db->get_where($this->_table_name, $where, 1, 0);
        if ($query->num_rows() == 0) { //Insert
            $this->db->insert($this->_table_name, $data);
            return;
        }
        //Existing data so update
        $this->db->update($this->_table_name, $data,$where);
    }
}


/* End of file user_access_map.php */
/* Location: ./ezRbac/models/user_access_map.php */