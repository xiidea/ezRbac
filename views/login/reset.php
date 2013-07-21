<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->ezuri->assets_url('css/style.css')?>" media="all" />
</head>
<body class="bg_c">
	
	<div class="login_wrapper">
    <div class="cf tac">
				<h3>XiiDEA</h3>
			</div>
            <p class="sepH_b"></p>
		<div class="loginBox">
			<div class="heading cf">
				<h3>Change Password</h3>
			</div>
			<div class="content">
				<div class="login_panes formEl_a">
					<div id="log_in_div">
						<p class="sepH_b"></p>
                        <?php if(isset($reset_success)) : ?>
                        <div class="msg_box">Password Changed Successfully!</div>
                        <div class="content_btm">
                            <?php echo anchor(site_url('/'),'Back To Home Page'); ?>
                        </div>
                        <?php else : ?>
						<form action="" method="post" class="formEl sepH_c" id="form_login">
                            <div class="msg_box msg_error" id="allErrors" style="display:<?php echo ($form_error) ? 'block' : 'none'?>"><?php echo $form_error?></div>
							<div class="sepH_a">
                                <label for="password" class="lbl_a">Password:</label>
                                <input type="password" id="password" name="password" class="inpt_a" />
							</div>
							<div class="sepH_b">
								<label for="re_password" class="lbl_a">Re-Type Password:</label>
								<input type="password" id="re_password" name="re_password" class="inpt_a" />
							</div>
							<div class="sepH_b">
                                <button class="btn_a btn" type="submit" name="ResetForm">Save</button>
							</div>

						</form>
                        <?php endif ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>