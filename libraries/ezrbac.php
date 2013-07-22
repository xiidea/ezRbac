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
       if(!$this->user){
           $user_id = $this->getCurrentUserID();
           if(!$user_id){
               return  null;
           }

           $this->user = $this->CI->ezuser->get_user_by_id($user_id);
       }
        return $this->user;
    }

    public function getCurrentUserID()
    {
        $user_id = $this->CI->session->userdata('user_id');
        if($user_id && !empty($user_id)){
            return $user_id;
        }

        return null;
    }

    public function setCurrentUser($user)
    {
       return $this->user = $user;
    }

    public function isGuest()
    {
        $guest = false;

        if($this->CI->session->userdata($this->CI->config->item('login_session_key', 'ez_rbac'))){
            $guest = !$this->getCurrentUserID();
        }

        return $guest;

    }

    public function createUser($data = array())
    {

        if(!(isset($data['user_role_id']) || isset($data['user_role']))){
            throw new Exception('You must provide user_role_id or user_role');
        }

        if(!isset($data['user_role_id'])){
            $data['user_role_id'] = $this->CI->user_role->create($data['user_role']);
        }

        return $this->CI->ezuser->create($data);
    }

    public function updateUser($data = array())
    {
        if(!set($data['id'])){
            throw new Exception('You must provide id');
        }

        if(isset($data['user_role'])){
            $data['user_role_id'] = $this->CI->user_role->create($data['user_role']);
        }

        return $this->CI->ezuser->update($data);
    }


}