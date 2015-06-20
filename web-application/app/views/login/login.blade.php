@extends('layouts.loginlayout')
<!-- Login page-->
@section('body')
<div id="facebook-Bar">
    <div id="facebook-Frame">
        <div id="logo"><img src="{{ URL::to('assets/images/DingDatt_logo_web1.png') }}" width="162" height="55" /></div>


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
    </div>
</div>

<div id="mb-header-main-right">
    <div class="btn dingclr fleft martop"><a href="{{ URL::to('profile') }}"><span id="txt_signup" class="txt_signup">Sign Up</span></a></div>
    <div class="btn dattclr fleft martop"><a href="{{ URL::to('login') }}"><span id="txt_login" class="txt_login">Login</span></a></div>

    <div class="lang fleft martop">
        {{ Form::select('language', array(''=>'Language')+$languageDetails,Session::get('language'), array('id'=> 'languageid','class'=>'radius sel_lang'))}}
    </div>
</div>
{{ Form::hidden('pagename','login', array('id'=> 'pagename')) }}
<!-- header ends here -->
<div class="clrfix"></div>

<div class="fscntxt_block">
    <h1><span id="txt_homehead">Welcome to Ding Datt</span></h1>  
    <p><span >Relax! We are all crazy- It's always a competition!</span></p>
    <img src="assets/images/smiley.gif"/>
</div>
<div class="Mb_tit"><h2 style="text-align:center;"><span id="txt_login" class="txt_login">Login</span></h2></div>
<div class="loginbox radius">

    <div class="loginboxinner radius">
        <div class="loginform">
            <div id="status" style="display:none;">
            </div>
            <p>
                <a href="facebook_login"><button class="facebook radius title" name="client_login"><span id="txt_face_signin">Signin using Facebook </span> </button></a>
            </p>
            <p>
                <a href="google_login"><button class="google radius title" name="client_login"><span id="txt_ggl_signin">Signin using Google+</span></button></a>
            </p>
            <p>
                <a href="#"><button class="instagram radius title" name="client_login"><span id="txt_pinterest_signin">Signin using Instagram</span></button></a>
            </p>
            {{ Form::open(array('url' => 'login', 'files'=> true, 'id' => 'login')) }}
            <?php
            if (Session::has('er_data')) {
                $er_data = Session::get('er_data');
            }
            ?>
            @if(Session::has('Message'))
            <p class="alert">{{ Session::get('Message') }}</p>
            @endif
            @if(isset($er_data['username']))
            <p class="alert" >{{ $er_data['username'] }}</p>
            @endif
            <p>
            <div class="inp_pfix"><img src="{{ URL::to('assets/images/user_icons.png') }}" width="25" height="25"></div>
            <input type="text" id="pch_useroremail" name="username" placeholder="User Name / Email" value="<?php echo Input::old('username'); ?>" class="radius pfix_mar" />
            </p>
            @if(isset($er_data['password']))
            <p class="alert">{{ $er_data['password'] }}</p>
            @endif
            <p>
            <div class="inp_pfix"><img src="{{ URL::to('assets/images/pass_icons.png') }}" width="25" height="25"></div>
            <input type="password" id="pch_password" name="password" placeholder="Password" class="radius pfix_mar" />
            </p>
            <p>
                <a href="{{ URL::to('forgot') }}"><span id="txt_forgotpassword">Forgot Password ?</span></a>
            </p>
            <p>
                <button class="radius title" name="client_login"><span id='txt_loginto'>Login to Ding Datt</span></button>
            </p>
            <div class="clrfix"></div>
            <p></p>

            </form>
        </div><!--loginform-->
    </div><!--loginboxinner-->
</div><!--loginbox-->

<div class="clrfix"></div>
<div class="ddwidth" style="margin-top:70px;">

    @endsection
