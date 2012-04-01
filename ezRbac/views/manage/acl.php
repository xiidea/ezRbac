<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <title>Manage Role Access</title>
     <link rel="stylesheet" type="text/css" href="<?php echo $this->ezuri->assets_url('css/style.css')?>" media="all" />
     <script type="text/javascript" src="<?php echo $this->ezuri->assets_url('js/jquery-1.6.2.min.js')?>"></script>
</head>
<body class="bg_c">
<div id="page" class="container">
	<div id="header">
		<div class="top-menus">
            <a href="https://github.com/xiidea/ezRbac/wiki">help</a> |
            <a href="https://github.com/xiidea/ezRbac">ezrbac</a> | 
            <?php echo $logout_url; ?>				
        </div>
        <div id="logo"><h3>Manage ezRbac ACL</h3></div>	
	</div><!-- header -->

	<div class="container">
        <div id="content">
            <h1>Role's Privilege Manager</h1>
            <p>This interface helps to manage the user's access Privilege according their role.</p>
            <div class="form">
            	<form id="frm_acl_gui" method="post" onsubmit="return false">
                	<div class="row">
						<label for="form_field_user_role">Select user Role<span class="required">*</span></label>		
               			<select size="1" name="user_role_id" id="form_field_user_role" style="width:250px;">
                        <option value="0">--select--</option>
                        <?php 
                         foreach ($access_roles->result() as $role) {
                            echo "<option value='$role->id'>$role->role_name</option>";
                        }
                        ?>
                     </select>
					</div>
                   <div class="row">
						<label for="form_field_access_node">Select user Role<span class="required">*</span></label>
               			<select size="1" name="controller" id="form_field_access_node" style="width:250px;">
                        <option value="0">--select--</option>
                        <?php
                         foreach ($controller_list as $controller) {
                            echo "<option value='$controller'>$controller</option>";
                        }
                        ?>
                     </select>
					</div>
                    <div class="row">
						<label>Chose Privileges<span class="required">*</span></label>
					</div>
                    <div class="clear"></div>
                    <?php foreach($access_list as $access) { ?>
                   <div class="row">
						<label class="secondlevel">Can <?php echo ucfirst($access); ?>?</label>
                        <input type="checkbox" name="permission[]" value="1" />
				   </div>
                   <?php } ?>
                </form>
            </div>
        </div><!-- content -->
	</div>
</div>

<div id="footer">
	Powered by <a rel="external" href="http://codeigniter.com/">CodeIgniter</a>.	<br>A product of <a rel="external" href="http://www.xiidea.net/">Xiidea</a>.
</div>
</body>
</html>