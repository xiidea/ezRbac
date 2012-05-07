<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * user_role
 *
 * This model represents user_role data. It can be used
 * for manipulation and retriving user_role data.
 *
 * @version	1.0
 * @package ezRbac
 * @since ezRbac v 0.3
 * @author Roni Kumar Saha<roni.cse@gmail.com>
 * @copyright Copyright &copy; 2012 Roni Saha
 * @license	GPL v3 - http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class user_role extends  CI_Model {
    /**
     * @var CI_Controller CI instance reference holder
     */
    private $CI;

    /**
     * @var String $_user_role_table store user_role_table name
     */
    private $_table_name;

    /*
     * Helpful to adapt your db without modifying the code!!
     */
    private $_schema_map=array(
        'id'    => 'id',
        'role_name'=>'role_name'
    );


    /**
     * Constructor function
     */
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->CI = & get_instance();
        $this->_table_name=$this->CI->config->item('user_role_table','ez_rbac');
        $schema=$this->CI->config->item('schema_user_role','ez_rbac');
        ($schema) AND $this->_schema_map=$schema;
    }

    /**
     * Get all existing user role saved in database
     * @return array
     */
    function get_role_list(){
        $query = $this->db->get($this->_table_name);
        $retarr=array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row){
                $retarr[]=array(
                    'id'=>$row->{$this->_schema_map['id']},
                    'role_name'=>$row->{$this->_schema_map['role_name']}
                );
            }
        }
        return $retarr;
    }
}


/* End of file user_role.php */
/* Location: ./ezRbac/models/manage/user_role.php */