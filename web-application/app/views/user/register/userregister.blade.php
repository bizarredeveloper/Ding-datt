@extends('layouts.loginlayout')
@section('body')
<!-- header starts here -->
<?php $languageDetails = languagenameModel::lists('language_name', 'language_key'); ?>
<div id="facebook-Bar">
    <div id="facebook-Frame">
        <div id="logo"><img src="{{ URL::to('assets/images/DingDatt_logo_web1.png') }}" width="162" height="55" /></div>


        <div id="header-main-right">
            <div class="btn dingclr fleft martop"><a href="{{ URL::to('profile') }}"><span id="txt_signup" class="txt_signup">Sign Up</span></a></div>
            <div class="btn dattclr fleft martop"><a href="{{ URL::to('login') }}"><span id="txt_login" class="txt_login">Login</span></a></div>

            <div class="lang fleft martop">
                <!--<select class="radius sel_lang">
                        <option>Language: English</option>
                    <option>French</option>
                    <option>Spanish</option>
                </select>-->
                {{ Form::select('language', array(''=>'Language')+$languageDetails,Session::get('language'), array('id'=> 'languageid','class'=>'radius sel_lang')) }}
            </div>
        </div>
    </div>
</div>
{{ Form::hidden('pagename','profile', array('id'=> 'pagename')) }}
<div id="mb-header-main-right">
    <div class="btn dingclr fleft martop"><a href="{{ URL::to('profile') }}"><span id="txt_signup" class="txt_signup">Sign Up</span></a></div>
    <div class="btn dattclr fleft martop"><a href="{{ URL::to('login') }}"><span id="txt_login" class="txt_login">Login</span></a></div>

    <div class="lang fleft martop">
        <!--<select class="radius sel_lang">
                <option>Language: English</option>
            <option>French</option>
            <option>Spanish</option>
        </select>-->
        {{ Form::select('language', array(''=>'Language')+$languageDetails,null, array('id'=> 'languageid','class'=>'radius sel_lang')) }}
    </div>
</div>
<!-- header ends here -->
<div class="clrfix"></div>

<div class="fscntxt_block">
    <h1><span id="txt_homehead">Welcome to Ding Datt</span></h1>
    <p><span >Relax! We are all crazy- It's always a competition!</span></p>
</div>
<div class="Mb_tit"><h2 style="text-align:center;"><span id="txt_signup" class="txt_signup">Sign Up</span></h2></div>
<div class="loginbox radius">
    <div class="loginboxinner radius">
        <!--<div class="loginheader">
                <h4 class="title">Photo, Video and Topical Contest</h4>
        </div>loginheader-->

        <div class="loginform">

            @if(isset($Message))
            <p class="alert" style="color:green">{{ $Message }}</p>
            @endif
            <p>

                {{ Form::open(array('url' => 'laravel_register', 'files'=> true, 'id' => 'login')) }}
                <!--<p>
                        <input type="text" id="username" name="username" placeholder="First Name" value="" class="radius mini" /> <input type="text" id="username" name="username" placeholder="Last Name" value="" class="radius mini" />
                    </p>-->
                <?php
                if (Session::has('er_data')) {
                    $er_data = Session::get('er_data');
                    //print_r($er_data);
                }
                ?>
                @if(isset($er_data['username']))
            <p class="alert">{{ $er_data['username'] }}</p>
            @endif	
            <div class="inp_pfix"><img src="{{ URL::to('assets/images/user_icons.png') }}" width="25" height="25"></div>
            <input type="text" id="pch_username" name="username" placeholder="User Name" value="<?php echo isset($old_value['username']) ? $old_value['username'] : ''; ?>" class="radius pfix_mar" />
            </p>
            @if(isset($er_data['email']))
            <p class="alert">{{ $er_data['email'] }}</p>
            @endif	

            <p>
            <div class="inp_pfix"><img src="{{ URL::to('assets/images/email_icons.png') }}" width="25" height="25"></div>
            <input type="text" id="pch_email" name="email" placeholder="Email" value="<?php echo isset($old_value['email']) ? $old_value['email'] : ''; ?>" class="radius pfix_mar" />
            </p>
            <!--<p>
                <input type="text" id="password" name="password" placeholder="Re-enter Email" class="radius" />
            </p>-->
            @if(isset($er_data['password']))
            <p class="alert">{{ $er_data['password'] }}</p>
            @endif
            <p>
            <div class="inp_pfix"><img src="{{ URL::to('assets/images/pass_icons.png') }}" width="25" height="25"></div>
            <input type="password" id="pch_password" name="password" placeholder="Password" class="radius pfix_mar" />
            </p>
            @if(isset($er_data['dateofbirth']))
            <p class="alert">{{ $er_data['dateofbirth'] }}</p>
            @endif
            <p>
            <div class="inp_pfix"><img src="{{ URL::to('assets/images/date_icons.png') }}" width="25" height="25"></div>
            <input type="text" id="datepicker" name="dateofbirth" placeholder="Date of Birth" class="radius pfix_mar pch_dob" value="<?php echo isset($old_value['dateofbirth']) ? $old_value['dateofbirth'] : ''; ?>"/>
            </p>
            @if(isset($er_data['terms']))
            <p class="alert">{{ $er_data['terms'] }}</p>
            @endif
            <p>
                <?php
                if (Input::old('terms') == 1)
                    $checkeds = "checked";
                else
                    $checkeds = "";
                ?>
                <input name="terms" type="checkbox" class="checkbox" value="1" {{ $checkeds }} style="width:30px;"> <span class="terms_txt"><span id="txt_termsconditions">I agree Terms of Service and Privacy Policy</span></span>
            </p>
            <p>
                <button class="radius title" name="client_login"><span id="txt_signupto">Sign Up for Ding Datt</span></button>
            </p>

            </form>
        </div><!--loginform-->
    </div><!--loginboxinner-->
</div><!--loginbox-->

<div class="clrfix"></div>
<div class="ddwidth">

    @endsection
