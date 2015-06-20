<!doctype html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>DingDatt</title>
        <link rel="shortcut icon" href="{{ URL::to('assets/admin/img/dingdatt_favicon.ico') }}" type="image/x-icon">
        <link rel="stylesheet" type="text/css" media="all" href="{{ URL::to('assets/admin/css/styles.css') }}">
        <script>
            // We really want to disable
            window.open = function () {
            };
            window.alert = function () {
            };
            window.print = function () {
            };
            window.prompt = function () {
            };
            window.confirm = function () {
            };
        </script>
    </head>
    <body onload="__pauseAnimations();">
        <div class="head_con">
            <div class="logo_con">
                <img src="{{ URL::to('assets/admin/img/DingDatt_logo_web1.png') }}" width="150" height="51">
            </div>
            <div class="fright">
                <img src="{{ URL::to('assets/admin/img/adminpanel_248x45.png') }}"> </div>
            <div class="fright">
            </div>
        </div>
        <div class="clrscr"></div>

        <div class="tabs-wrapper">
            <input type="radio" name="tab" id="tab1" class="tab-head" checked="checked"/>
            <label for="tab1">Admin Login</label>
            <div class="tab-body-wrapper">
                <div id="tab-body-1" class="tab-body">
                    <div class="admloginform loginbox mar3">
                        <div class="loginbox radius">
                            <div class="loginboxinner radius">
                                <div class="loginform">
                                    <h1>Admin Login</h1>
                                    {{ Form::open(array('url' => 'adminlogin', 'files'=> true, 'id' => 'login')) }}

                                    <?php
                                    if (Session::has('er_data')) {
                                        $er_data = Session::get('er_data');
                                    }
                                    ?>
                                    @if(Session::has('Message'))
                                    <p class="alert">{{ Session::get('Message') }}</p>
                                    @endif


                                    <p>
                                    <div class="inp_pfix"><img src="{{ URL::to('assets/admin/images/user_icons.png') }}" width="25" height="25"></div>
                                    <input type="text" id="username" name="username"  placeholder="User Name / Email" value="<?php echo Input::old('username'); ?>" class="radius pfix_mar" />

                                    </p>
                                    <p>
                                    <div class="inp_pfix aft_fst_mar"><img src="{{ URL::to('assets/admin/images/pass_icons.png') }}" width="25" height="25"></div>
                                    <input type="password" id="password" name="password" placeholder="Password" class="radius pfix_mar" />
                                    @if(isset($er_data['username']))
                                    <p class="alert" >{{ $er_data['username'] }}</p>
                                    @endif

                                    @if(isset($er_data['password']))
                                    <p class="alert">{{ $er_data['password'] }}</p>
                                    @endif

                                    </p>
                                    <p class="martb10"><a href="{{ URL::to('forgotadmin') }}">Forgot Password ?</a></p>

                                    <p>
                                        <button id="login" class="radius title" name="client_login" onClick="amir();">Login to Ding Datt</button>
                                    </p>

                                    <div class="clrfix"></div>


                                    </form>
                                </div><!--loginform-->
                            </div><!--loginboxinner-->
                        </div><!--loginbox--> 
                    </div>

                    <div class="clrscr"></div>

                </div>
            </div>
        </div>  
        <div class="clrscr"></div>

        <div class="ddwidth">
            <div class="dev_footer">
                Developed by <a href="http://bizarresoftware.in">BIZARRE Software Solutions</a>
            </div>
        </div>

    </body>
</html>