@extends('layouts.loginlayout')
<!-- Forgot username page -->
@section('body')
<div id="facebook-Bar">
    <div id="facebook-Frame">
        <div id="logo"><img src="{{ URL::to('assets/images/DingDatt_logo_web1.png') }}" width="162" height="55" /></div>

        <?php
        if (Session::has('admin')) {
            $admin = Session::get('admin');
        }
        if ($admin == 0) {
            ?> 
            <div id="header-main-right">
                <div class="btn dingclr fleft martop"><a href="{{ URL::to('profile') }}"><span id="txt_signup" class="txt_signup">Sign Up</span></a></div>
                <div class="btn dattclr fleft martop"><a href="{{ URL::to('login') }}"><span id="txt_login" class="txt_login">Login</span></a></div>

                <div class="lang fleft martop">
                    <?php
                    $languageDetails = languagenameModel::lists('language_name', 'language_key');
                    ?>
                    {{ Form::select('language', array(''=>'Language')+$languageDetails,Session::get('language'), array('id'=> 'languageid','class'=>'radius sel_lang')) }}
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php if ($admin == 0) { ?>
    <div id="mb-header-main-right">
        <div class="btn dingclr fleft martop"><a href="{{ URL::to('profile') }}"><span id="txt_signup" class="txt_signup">Sign Up</span></a></div>
        <div class="btn dattclr fleft martop"><a href="{{ URL::to('login') }}"><span id="txt_login" class="txt_login">Login</span></a></div>

        <div class="lang fleft martop">

            {{ Form::select('language', array(''=>'Language')+$languageDetails,Session::get('language'), array('id'=> 'languageid','class'=>'radius sel_lang'))}}
        </div>
    </div>
<?php } ?>

{{ Form::hidden('pagename','forgotpass', array('id'=> 'pagename')) }}
<!-- header ends here -->
<div class="clrfix"></div>

<div class="fscntxt_block">
    <h1><span id="txt_homehead">Welcome to Ding Datt</span></h1>
    <p><span> <!--id="txt_homepage"--> Relax! We are all crazy- It's always a competition!</span></p>
</div>
<div class="Mb_tit"><h2 style="text-align:center;"><span id="txt_login" class="txt_forgotpassword">Forgot Password</span></h2></div>
<div class="loginbox radius">

    <div class="loginboxinner radius">
        <div class="loginform">
        </div>
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
        <?php if ($admin == 1) { ?> <a href="{{ URL::to('admin') }}"  style="text-decoration:none;">back</a><?php } ?>
        <div class="clrfix"></div>
        <input type="hidden" name="admin" value="{{ $admin }}" />
        </form>
    </div><!--loginform-->
</div><!--loginboxinner-->
</div><!--loginbox-->

<div class="clrfix"></div>
<div class="ddwidth" style="margin-top:270px;">
    @endsection