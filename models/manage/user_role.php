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
    private $_schema=array(
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
        ($schema) AND $this->_schema=$schema;
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
                    'id'=>$row->{$this->_schema['id']},
                    'role_name'=>$row->{$this->_schema['role_name']}
                );
            }
        }
        return $retarr;
    }

    public function get_role_id($roleName = ""){
        $this->db->like($this->_schema['role_name'], $roleName, 'none');

        $query = $this->db->get($this->_table_name, 1);

        if($query->num_rows() > 0){
            $result = $query->result();
            return $result[0]->{$this->_schema['id']};
        }

        return null;

    }

    public function get_role_by_id($id=null)
    {
        if(empty($id)){
            return null;
        }

        $this->db->where(array($this->_schema['id'] => $id));
        $query = $this->db->get($this->_table_name, 1);

        if($query->num_rows() > 0){
            $result = $query->result();
            return $result[0]->{$this->_schema['role_name']};
        }

        return null;

    }

    public function create($roleName = ""){
       if($roleName == ""){
           return null;
       }

       $role_id = $this->get_role_id($roleName);

        if(!$role_id){
            $this->db->insert($this->_table_name, array($this->_schema['role_name']=> $roleName ));
            $role_id =  $this->db->insert_id();
        }

        return $role_id;
    }

    public function update($id = null, $roleName = ""){
        if((!$id) || $roleName == "" ){
            return null;
        }

        $role_id = $this->get_role_id($roleName);

        if($role_id && $id !== $role_id){
            throw new Exception('Role name already exist');
        }

        $this->db->update($this->_table_name,
                        array($this->_schema['role_name']=> $roleName ),
                        array($this->_schema['id'] => $id));

    }

}


/* End of file user_role.php */
/* Location: ./ezRbac/models/manage/user_role.php */