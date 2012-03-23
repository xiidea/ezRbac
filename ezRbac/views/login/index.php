<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <title>Login</title>
    <style>
        html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, font, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td {
            border: 0;
            outline: 0;
            font-size: 100%;
            vertical-align: baseline;
            background: transparent;
            margin: 0;
            padding: 0
        }

        body {
            line-height: 1
        }

        :focus {
            outline: 0
        }

        html {
            overflow-y: scroll;
            font-size: 100%;
            color: #222
        }

        body {
            min-height: 100%;
            font: 100 12px/1.3 Helvetica, Arial, sans-serif;
            padding-bottom: 10px
        }

        h1, h2, h3, h4 {
            font-weight: 400;
            color: #222
        }

        h1, h2, h3 {
            font-family: 'Open Sans', sans-serif
        }

        h3 {
            font-size: 18px
        }

        a {
            text-decoration: none;
            color: #21759B
        }

        a:hover {
            text-decoration: underline
        }

            /* main styles */

        .dn {
            display: none
        }

        .fr {
            float: right
        }

        .fl {
            float: left
        }

        .tac {
            text-align: center
        }

        .tar {
            text-align: right
        }

        .taj {
            text-align: justify
        }

        .bld {
            font-weight: 700
        }

            /* buttons */
        .btn {
            border: none;
            outline: 0;
            cursor: pointer;
            text-decoration: none;
            overflow: visible;
            background: none;
            padding: 0 16px;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            color: #222
        }

        button::-moz-focus-inner {
            border: none;
            margin: 0;
            padding: 0
        }

        .btn:hover {
            text-decoration: none
        }

        .btn_a {
            border: 1px solid #c6c6c6;
            text-shadow: 1px 1px 0 #fff
        }

        .btn_a {
            font: 700 12px/25px Helvetica, Arial, sans-serif
        }

        .btn_a:hover {
            background: #e3e3e3
        }

            /* no black border for buttons (<ie7) */
        button {
            *border: none !important;
            *border-color: transparent !important
        }

        .btn span.btnImg {
            background-position: left center;
            padding-left: 25px;
            background-repeat: no-repeat
        }

            /* element separation */
        .sepH_a {
            margin-bottom: 6px
        }

        .sepH_b {
            margin-bottom: 12px
        }

        .sepH_c {
            margin-bottom: 24px
        }

            /* alert boxes */
        .msg_box {
            padding: 8px 20px 8px 20px;
            margin-bottom: 10px;
            position: relative;
            color: #444;
            overflow: hidden
        }

        .msg_error {
            background: #f2cac2;
            border: 1px solid #ae432e
        }

            /* forms */
        .formEl_a input {
            background: #fff;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 13px
        }

        .formEl_a input:focus, .formEl_a input:active {
            background: #ebfdd7
        }

        .formEl_a label {
            cursor: pointer
        }

        .formEl_a .lbl_a {
            display: block;
            margin-bottom: 4px;
            color: #666;
            font-weight: 700
        }

        .formEl_a .lbl_c {
            font-size: 13px;
            vertical-align: middle;
            color: #666
        }

        .formEl_a .inpt_a {
            padding: 5px 8px;
            width: 380px;
            border: 1px;
            border-color: #b8b8b8 #e2e2e2 #e2e2e2 #b8b8b8;
            border-style: solid
        }

        .formEl_a .inpt_c {
            width: 13px;
            height: 13px;
            margin: 0 6px 0 0;
            padding: 0;
            vertical-align: middle
        }

        .formEl_a div.error > label {
            color: #9d261d
        }

        .formEl_a div.error input[type=text], .formEl_a div.error input[type=password], .formEl_a div.error textarea {
            border-color: #c87872;
            background: #FAE5E3
        }

            /* clear floats */
        .cf {
            *zoom: 1
        }

        .cf:before, .cf:after {
            content: "";
            display: table
        }

        .cf:after {
            clear: both
        }

            /* cross browser inline-block display */
        .btn, .btn span {
            display: -moz-inline-stack;
            display: inline-block;
            zoom: 1;
            *display: inline
        }

            /* login page */
        .login_wrapper {
            width: 372px;
            margin: 0 auto;
            padding: 180px 0 0;
        }

        .loginBox {
            background: #fff;
            border: 1px solid #ccc
        }

        .loginBox .heading {
            margin-bottom: 16px;
            padding: 4px 16px;
            border-bottom: 1px solid #ccc;
            -moz-border-radius-topleft: 4px;
            -moz-border-radius-topright: 4px;
            -moz-border-radius-bottomright: 0px;
            -moz-border-radius-bottomleft: 0px;
            -webkit-border-radius: 4px 4px 0px 0px;
            border-radius: 4px 4px 0px 0px
        }

        .loginBox .heading img {
            padding-top: 4px
        }

        .loginBox .content {
            padding: 0 16px 10px
        }

        .loginBox .content_btm {
            padding: 4px 0 0;
            border-top: 1px solid #ccc
        }

        .loginBox .inpt_a {
            width: 320px
        }

            /* form errors */
        form .msg_box label {
            display: block;
            padding: 2px 0
        }

            /* css3 enhance */

            /* css3 border-radius */
        .formEl_a .inpt_a, .formEl_a .inpt_b,
        .loginBox {
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px
        }

            /* css3 gradients */
        .loginBox .heading {
            background: #ececec;
            background: -moz-linear-gradient(top, #f9f9f9 0%, #ececec 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #f9f9f9), color-stop(100%, #ececec));
            background: -webkit-linear-gradient(top, #f9f9f9 0%, #ececec 100%);
            background: -o-linear-gradient(top, #f9f9f9 0%, #ececec 100%)
        }

            /* css3 box shadow */
        .loginBox {
            -webkit-box-shadow: 0px 0px 4px 1px #ddd;
            -moz-box-shadow: 0px 0px 4px 1px #ddd;
            box-shadow: 0px 0px 4px 1px #ddd
        }
    </style>


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
                            <label for="username" class="lbl_a">Email:</label>
                            <input type="text" id="username" name="username" class="inpt_a"
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
<script type="text/javascript">
    display = {
        login:function () {
            document.getElementById('log_in_div').style.display = '';
            document.getElementById('get_password_div').style.display = 'none';
        },
        password:function () {
            document.getElementById('log_in_div').style.display = 'none';
            document.getElementById('get_password_div').style.display = '';
        }
    }
</script>
</body>
</html>