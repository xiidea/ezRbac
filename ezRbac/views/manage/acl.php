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
            	<form id="frm_acl_gui" method="post">
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
                    <?php foreach($access_list as $access) { ?>
                   <div class="row">
						<label>Can <?php echo $access; ?>?</label>
                        <input type="checkbox" name="permission[]" value="1" />
				   </div>
                   <?php } ?>
                </form>
            </div>
            <form name="frm_input">
<table border="0" width="100%" cellpadding="3" style="border-collapse: collapse" bgcolor="#F5F5F5">
	<tr class="tbl_even">
		<td width="7">&nbsp;</td>
		<td width="200">&nbsp;</td>
		<td width="6" style="font-weight: bold"></td>
		<td>&nbsp;</td>
		<td width="7">&nbsp;</td>
	</tr>
    <tr class="tbl_odd">
		<td width="7">&nbsp;</td>
		<td width="200">select user group<span class="mfield">*</span></td>
		<td width="6" align="center" style="font-weight: bold">:</td>
		<td> 
			<select size="1" name="user_group" id="user_group" onchange="change_privilege(this.value);">
				<option value="0">--select--</option>
				<?php 
				 foreach ($access_roles->result() as $role) {
					echo "<option value='$role->id'>$role->role_name</option>";
                }
                ?>
             </select>
		</td>
		<td width="7">&nbsp;</td>
	</tr>
    <tr class="tbl_title">
		<td width="7">&nbsp;</td>
		<td width="200">&nbsp;&nbsp;menu privileges</td>
		<td width="6" style="font-weight: bold">&nbsp;</td>
		<td align="right"><input type="button" value="update access privilege" ></td>
		<td width="7">&nbsp;</td>
	</tr>
    <tr class="tbl_even">
		<td width="7">&nbsp;</td>
		<td colspan="3">
        <div id="test">
        </div>
        <table width="100%" border="1" bordercolor="#e8661b" cellpadding="0" style="border-collapse: collapse">
		  <tr class="tbl_heading" >
			<td align="center"  valign="middle" width="58">sl no.</td>
            <td align="left">&nbsp;&nbsp;menu name</td>
            <td width="55" align="center"  valign="middle">level</td>
            <td align="left">&nbsp;&nbsp;menu under</td>
            
            <td align="left" width="120">&nbsp;&nbsp;menu position</td>
			<td width="90" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input title="Select all" type="checkbox" name="ch_view" onclick='chk_checkUncheck(document.getElementsByName("ch_view"))'>view</td>
			<td width="90" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input title="Select all" type="checkbox" name="ch_upd" onclick='chk_checkUncheck(document.getElementsByName("ch_upd"))'>update</td>
			<td width="90" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input title="Select all" type="checkbox" name="ch_del" onclick='chk_checkUncheck(document.getElementsByName("ch_del"))'>delete</td>
            <td width="90" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input title="Select all" type="checkbox" name="ch_add" onclick='chk_checkUncheck(document.getElementsByName("ch_add"))'>add</td>
		  </tr>
		  <tr >
          <td align="center" valign="middle" style="font-size:11px"><strong></strong></td>
          <td></td> 
          <td align="center"></td>
          <td style="padding-left:5px; padding-right:5px"></td> 
          <td></td>     
			<td align="left" onmouseover="ddrivetip('view')" onmouseout="hideddrivetip()">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ch_view" id="v_" value="" onclick="return chk_allchecked(this)" ></td>  
            <td align="left" onmouseover="ddrivetip('update')" onmouseout="hideddrivetip()">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ch_upd" id="u_" ></td>
            <td align="left" onmouseover="ddrivetip('delete')" onmouseout="hideddrivetip()">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ch_del" id="d_" ></td>
            <td align="left" onmouseover="ddrivetip('add')" onmouseout="hideddrivetip()">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ch_add" id="a_" ></td>
		  </tr>
		</table>
        
        </td>
		<td width="7">&nbsp;</td>
	</tr>
    <tr class="tbl_title">
		<td width="7">&nbsp;</td>
		<td width="200">&nbsp;</td>
		<td width="6" style="font-weight: bold"></td>
		<td align="right"><input type="button" onmouseover="this.className='nav_hover'" onmouseout="this.className='nav'"class="nav" value="update access privilege" onclick="access_control_check();"></td>
		<td width="7">&nbsp;</td>
	</tr>
	<tr class="tbl_even">
		<td width="7">&nbsp;</td>
		<td width="200">&nbsp;</td>
		<td width="6" style="font-weight: bold"></td>
		<td>&nbsp;</td>
		<td width="7">&nbsp;</td>
	</tr>
</table>
</form>
            
        </div><!-- content -->
	</div>
</div>

<div id="footer">
	Powered by <a rel="external" href="http://codeigniter.com/">CodeIgniter</a>.	<br>A product of <a rel="external" href="http://www.xiidea.net/">Xiidea</a>.
</div>
</body>
</html>