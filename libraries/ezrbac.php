<?php
/**
 * @Author: Roni Kumar Saha
 * Date: 7/21/13
 * Time: 10:06 AM
 */

class ezrbac {

    private $CI;
    private $user;

    public function __construct(){
        $this->CI = & get_instance();
        $this->CI->load->model('ezuser');
        $this->CI->load->model('manage/user_role');
    }

    public function getCurrentUser()
    {
       return $this->user;
    }

    public function getCurrentUserID()
    {
        if(!$this->user){
            return false;
        }

        return $this->ezuser->getUserID($this->user);
    }

    public function setCurrentUser($user)
    {
       return $this->user = $user;
    }

    public function createUser($data = array())
    {

        if(!isset($data['user_role_id']) || !isset($data['user_role'])){
            throw new Exception('You must provide user_role_id or user_role');
        }

        if(!isset($data['user_role_id'])){
            $data['user_role_id'] = $this->user_role->create($data['user_role']);
        }

        return $this->ezuser->create($data);
    }

    public function updateUser($data = array())
    {
        if(!set($data['id'])){
            throw new Exception('You must provide id');
        }

        if(isset($data['user_role'])){
            $data['user_role_id'] = $this->user_role->create($data['user_role']);
        }

        return $this->ezuser->update($data);
    }


}