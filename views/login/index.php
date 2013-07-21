<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->ezuri->assets_url('css/style.css')?>" media="all" />
    <script type="text/javascript" src="<?php echo $this->ezuri->assets_url('js/login_js.js')?>"></script>
</head>
<body class="bg_c">

<div class="login_wrapper">
    <div class="cf tac">
        <h3>XiiDEA</h3>
    </div>
    <p class="sepH_b"></p>

    <div class="loginBox">
        <div class="heading cf">
            <h3>Login</h3>
        </div>
        <div class="content">
            <div class="login_panes formEl_a">
                <div
                    id="log_in_div" <?php if ($this->input->post('action') == 'recover_password') echo "style='display:none'" ?>>
                    <p class="sepH_b"></p>

                    <form action="" method="post" class="formEl sepH_c" id="form_login">
                        <?php if ($this->input->post('action') != 'recover_password'): ?>
                        <div class="msg_box msg_error" id="allErrors"
                             style="display:<?php echo ($form_error) ? 'block' : 'none'?>"><?php echo $form_error?></div>
                        <?php endif; ?>
                        <div class="sepH_a">
                            <label for="username" class="lbl_a">Email:</label>
                            <input type="text" id="username" name="username" class="inpt_a"
                                   value="<?php echo $this->input->post('username') ?>"/>
                            <input type="hidden" name="action" value="login"/>
                        </div>
                        <div class="sepH_b">
                            <label for="password" class="lbl_a">Password:</label>
                            <input type="password" id="password" name="password" class="inpt_a"/>
                        </div>
                        <div class="sepH_b">
                            <input type="checkbox" class="inpt_c" id="remember"
                                   name="remember" <?php if ($this->input->post('remember')) echo 'checked="checked" '?>/>
                            <label for="remember" class="lbl_c">Remember me</label>
                            <button class="btn_a btn fr" type="submit">Login</button>
                        </div>

                    </form>
                    <div class="content_btm">
                        <a href="javascript:" onclick="display.password()">Forgot your password?</a>
                    </div>
                </div>
                <div
                    id="get_password_div" <?php if ($this->input->post('action') != 'recover_password') echo "style='display:none'" ?>>
                    <?php if (isset($reset_email_confirm)): ?>
                    <p class="sepH_b"> Please Check your email! An email has been set with the instruction to reset your
                        password!</p>
                    <?php else: ?>
                    <p class="sepH_b">Please enter your email address. You will receive a link to create a new password
                        via email.</p>
                    <?php endif ?>
                    <form method="post" class="formEl sepH_c" name="frm_recover">
                        <?php if ($this->input->post('action') == 'recover_password'): ?>
                        <div class="msg_box msg_error" id="allErrors"
                             style="display:<?php echo ($form_error) ? 'block' : 'none'?>"><?php echo $form_error?></div>
                        <?php endif; ?>
                        <div class="sepH_b">
                            <label for="forget_username" class="lbl_a">Email:</label>
                            <input type="text" id="forget_username" name="username" class="inpt_a"
                                   value="<?php echo $this->input->post('username') ?>"/>
                            <input type="hidden" name="action" value="recover_password"/>
                        </div>
                        <button class="btn_a btn">Get new password</button>
                    </form>
                    <div class="content_btm">
                        <a href="javascript:" onclick="display.login()">Back to login</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</body>
</html>