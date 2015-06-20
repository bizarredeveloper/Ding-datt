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
            <label for="tab1">Forgot password</label>

            <div class="tab-body-wrapper">
                <div id="tab-body-1" class="tab-body">

                    <div class="admloginform loginbox mar3">
                        <div class="loginbox radius">

                            <div class="loginboxinner radius">

                                <div class="loginform">
                                    <h1>Forgot password</h1>

                                    <?php
                                    if (Session::has('er_data')) {
                                        $er_data = Session::get('er_data');
                                    }

                                    if (Session::has('admin')) {
                                        $admin = Session::get('admin');
                                    }
                                    ?>
                                    @if(Session::has('Message'))
                                    <p class="alert">{{ Session::get('Message') }}</p>
                                    @endif

                                    {{ Form::hidden('pagename','forgotpass', array('id'=> 'pagename')) }}


<?php if ($admin == 0) { ?>
                                        {{ Form::open(array('url' => 'forgotpasswordprocess', 'files'=> true, 'id' => 'login')) }}
                                    <?php } else { ?>			
                                        {{ Form::open(array('url' => 'ForgotPasswordProcessforadmin', 'files'=> true, 'id' => 'login')) }}
                                    <?php } ?>
                                    <?php
                                    if (Session::has('er_data')) {
                                        $er_data = Session::get('er_data');
                                    }
                                    ?>
                                    <?php if ($admin == 0) { ?>
                                        @if(Session::has('Message'))
                                        <p class="alert">{{ Session::get('Message') }}</p>
                                        @endif 
                                    <?php } else { ?>

                                        @if(Session::has('Messageadmin'))
                                        <p class="alert">{{ Session::get('Messageadmin') }}</p>						
                                        @endif
<?php } ?>
                                    <p>
                                    <div class="inp_pfix"><img src="{{ URL::to('assets/images/user_icons.png') }}" width="25" height="25"></div>
                                    <input type="text" id="pch_useroremail" name="username" placeholder="User Name / Email" value="<?php echo Input::old('email'); ?>" class="radius pfix_mar" />
                                    </p>

                                    <p>
                                        <button class="radius title" name="client_login"><span id='txt_resendpassword'>Resend Password</span></button>

                                    </p>
<?php if ($admin == 1) { ?><p class="martb10">
                                            <a href="{{ URL::to('admin') }}"  style="text-decoration:none;" >Back to login</a></p><?php } ?>
                                    <div class="clrfix"></div>
                                    <input type="hidden" name="admin" value="{{ $admin }}" />
                                    </form>

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