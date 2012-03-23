<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//List of public controller which access will not be checked by our library
$config['public_controller']=array('welcome');

// Message to return while performing an ajax request
$config['ajax_no_permission_msg']="You do not have permission to perform this action!";

//The default access for a unknown method TRUE for give access denied otherwise
$config['default_access']=FALSE;

//Access Map array Used by the system
$config['default_access_map']=array("publish","delete","edit","create","view");;

//The remember time for user login with remember me checked
$config['autologin_cookie_life']=24*60*60*30;

//The redirect url if access denied for a resource url, should be a public controller
// Leve it empty if you are not sure about this
$config['redirect_url']="";

//The password validation rule check for minimum password length
$config['password_min_length']=6;

//The autologin cookie name used to store user data
$config['autologin_cookie_name']="ezrbac_remember_me";

//@TODO Required When Implemented the Rbac Management system
$config['ezrbac_password']="hardtoremember";


/* End of file ez_rbac.php */
/* Location: ./ezRbac/config/ez_rbac.php */
