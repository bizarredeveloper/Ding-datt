@extends('header.header')
@section('includes')
<!-- multi select -->
<script src="{{ URL::to('assets/inner/js/jquery.sumoselect.js') }}"></script>
<link href="{{ URL::to('assets/inner/css/sumoselect.css') }}" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function () {
    window.asd = $('.SlectBox').SumoSelect({csvDispCount: 3});
    window.test = $('.testsel').SumoSelect({okCancelInMulti: true});
});
</script>
<!-- multi select -->
<!-- date time picker -->
<link rel="stylesheet" media="all" type="text/css" href="{{ URL::to('assets/inner/css/jquery-ui.css') }}" />
<link rel="stylesheet" media="all" type="text/css" href="{{ URL::to('assets/inner/css/jquery-ui-timepicker-addon.css') }}" />
<script src="{{ URL::to('assets/inner/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/jquery-ui-timepicker-addon.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/jquery-ui-sliderAccess.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/date_time_script.js') }}"></script>
<script>
function imageIsLoaded(e) {

    var image = new Image();
    image.src = e.target.result;
    $(".roundedimgjoin").attr('src', e.target.result);
}
$(document).ready(function (e) {

    $(document).on("change", "#themephoto", function () {
        console.log("The text has been changed.");
        var file = this.files[0];
        console.log(file);
        var imagefile = file.type;
        var filesize = file.size / (1024 * 1024);

        if (filesize > 2)
        {
            $("#theam_error").html("The maximum 2MB images can be upload.");
            $("#theam_error1").val("The maximum 2MB images can be upload.");
            return false;
        }
        else
        {
            $("#theam_error").html("");
            $("#theam_error1").val("");
        }
        var match = ["image/jpeg", "image/png", "image/jpg"];
        if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2])))
        {
            $("#theam_error").html("Upload only image file.");
            $("#theam_error1").val("Upload only image file.");
            $('.imgblink!').attr('src', 'noimage.png');
            return false;
        }
        else
        {
            $("#theam_error").html("");
            $("#theam_error1").val("");
            var reader = new FileReader();
            reader.onload = imageIsLoaded;
            reader.readAsDataURL(this.files[0]);
        }
        $("#txt_uploadcontestimage").html(file.name);
    });


    $(document).on("change", ".sponsorimgfile", function () {
        console.log("The text has been changed.");
        var file = this.files[0];
        console.log(file);
        var imagefile = file.type;
        var filesize = file.size / (1024 * 1024);

        if (filesize > 2)
        {
            $("#theam_error").html("The maximum 2MB images can be upload.");
            $("#theam_error1").val("The maximum 2MB images can be upload.");
            return false;
        }
        else
        {
            $("#theam_error").html("");
            $("#theam_error1").val("");
        }
        var match = ["image/jpeg", "image/png", "image/jpg"];
        if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2])))
        {
            $("#theam_error").html("Upload only image file.");
            $("#theam_error1").val("Upload only image file.");
            $('.imgblink!').attr('src', 'noimage.png');
            return false;
        }
        else
        {
            $("#theam_error").html("");
            $("#theam_error1").val("");
            var reader = new FileReader();
            reader.onload = imageIsLoadedspon;
            reader.readAsDataURL(this.files[0]);
        }

    });
});

function imageIsLoadedspon(e) {
    var image = new Image();
    image.src = e.target.result;
    $(".roundedimgsopn").attr('src', e.target.result);
}
</script>
@stop
@section('body')
<?php
$assets_path = "assets/inner/";
?>
{{ Form::hidden('pagename','contest', array('id'=> 'pagename')) }}
<div class="main_head"><span id="txt_createcontest" class="txt_createcontest">Create contest</span>

</div>
<div id="subtab_div" <?php if (Auth::user()->ID == 1) { ?> style="margin-right:85px;"   <?php } ?> class="con_cat_right1 mbnone" >
    <button class="bck_btn" onclick="goback()" >&laquo; <span class="txt_back" > Back </span> </button>
</div>
<div class="main_wrap">

    <form id="editprofile" name="contest" action="contest" enctype="multipart/form-data"  method="post" class="form_mid">
        @if(isset($Message))
        <p class="alert" style="color:green;padding:5px;text-align:center;font-size:13px">{{ $Message }}</p>
        @endif
        <?php
        if (Session::has('er_data')) {
            $er_data = Session::get('er_data');
        }
        ?>
        <div class="loginform loginbox mar1">
            <legend class="radius"><div class="leg_head"><span id="txt_contestinfo">Contest Info</span></div>

                <p>
                <div class="inp_pfix"><img src="{{ URL::to('assets/inner/img/bell_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_contestname" name="contest_name" placeholder="Contest Name" value="<?php echo Input::old('contest_name'); ?>" class="radius pfix_mar" />
                </p>
                @if(isset($er_data['contest_name']))
                <p class="alert" style="text-align:left;margin-left:55px;color:red;font-size:100%">{{ $er_data['contest_name'] }}</p>
                @endif

                <div class="rdogrp">
                    <label><strong><span id="txt_contesttype">Contest Type</span></strong></label>
                    <div class="mb_brk"></div>
                    <input type="radio" id="ct1" name="contesttype" value="p" <?php echo (Input::old('contesttype') == "" || Input::old('contesttype') == "p") ? "checked" : ""; ?> >
                    <label for="ct1" id="txt_photo">Photo</label>
                    <input type="radio" id="ct2" name="contesttype" value="v" <?php echo (Input::old('contesttype') == "v") ? "checked" : ""; ?>>
                    <label for="ct2" id="txt_video">Video</label>
                    <input type="radio" id="ct3" name="contesttype" value="t" <?php echo (Input::old('contesttype') == "t") ? "checked" : ""; ?>>
                    <label for="ct3" id="txt_topic">Topic</label>
                </div>
                @if(isset($er_data['contesttype']))
                <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['contesttype'] }}</p>
                @endif
                <div class="clrscr"></div>
                <div class="mbblk">
                    <select class="radius sel_lang">
                        <option>Photo Contest</option>
                        <option>Video Contest</option>
                        <option>Topic Contest</option>
                    </select>
                </div>
                <div class="mbblk">
                    <select class="radius sel_lang">
                        <option>Private</option>
                        <option>Current</option>
                        <option>Upcoming</option>
                        <option>Archive</option>
                    </select>
                </div>

                <p>
                    <script>
                        function updateuploadcontestimg(vals)
                        {
                            //$("#txt_uploadcontestimage").html(vals);
                        }
                    </script>
                <div class="img_pfix"><img src="{{ URL::to('assets/inner/img/upload_img.png') }}" width="45" height="45"  class="roundedimg roundedimgjoin"></div>				
                <label class="myLabel">
                    <input name="themephoto" type="file" id="themephoto" value="<?php echo Input::old('themephoto'); ?>" onchange="updateuploadcontestimg(this.value)" />
                    <span id="txt_uploadcontestimage"  >Upload Contest Image</span>
                </label>
                @if(isset($er_data['themephoto']))
                <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['themephoto'] }}</p>
                @endif
                <p id="theam_error" style='text-align:left;margin-left:55px;color:red;'></p>
                <input type="hidden" id="theam_error1">
                </p>
                <?php if (Auth::user()->ID == 1) { ?>
                    <p><div class="rdogrp">
                        <label><strong>Visibility</strong></label>						
                        <input type="radio" id="v2" name="visibility" value="u" <?php echo "checked"; ?>>
                        <label for="v2" >Public</label>
                        <input type="radio" id="v1" name="visibility" value="p" >
                        <label for="v1" id="txt_photo">Private</label>

                    </div></p>

                <?php } ?>
                <p>
                <div class="inp_pfix"<?php if (Auth::user()->ID == 1) { ?>style="margin-top:28px;"  <?php } ?>><img src="{{ URL::to('assets/inner/img/gender_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_nofopartis0" name="noofparticipant" placeholder="No. of Participants - 0 for Unlimited" value="<?php echo Input::old('noofparticipant'); ?>" class="radius pfix_mar" />
                </p>
                @if(isset($er_data['noofparticipant']))
                <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['noofparticipant'] }}</p>
                @endif

<!--<p>
        <div class="inp_pfix"><img src="{{ URL::to('assets/inner/img/prize_icons.png') }}" width="25" height="25"></div>
        <input type="text" id="pch_contestprize" name="prize" placeholder="Contest Prize" value="<?php echo Input::old('prize'); ?>" class="radius pfix_mar" />
</p>-->
                <!-- id="pch_contestinfo" -->
                <p>
                    <textarea name="description" cols="" id="pch_contestinfo" rows=""  class="radius" placeholder="Contest Information"><?php echo Input::old('description'); ?></textarea>
                </p>
            </legend>

            <legend class="radius"><div class="leg_head"><span id="txt_favorite">Favorite</span></div>
                <p>
                <div class="inp_pfix"><img src="{{ URL::to('assets/inner/img/interest_icons.png') }}" width="25" height="25"></div>
                <?php
                $user_id = Auth::user()->ID;
                $interestList = InterestCategoryModel::where('status', 1)->lists('Interest_name', 'Interest_id');
                $userInterest = userinterestModel::where('user_id', $user_id)->lists('interest_id');
                ?>
                {{ Form::select('interest[]', $interestList,Input::old('interest'), array('class'=>'SlectBox testsel radius','multiple'=>'multiple','placeholder'=>'Select Interest','onchange'=>'console.log($(this).children(":selected").length)')) }}
                </p>
            </legend>
        </div>
        <div class="loginform loginbox mar2">
            <legend class="radius">
                <div class="leg_head"><span id="txt_contestschedule">Contest Schedule</span></div>

                <p>
                <div class="inp_pfix"><img src="{{ URL::to('assets/inner/img/date_icons.png') }}" width="25" height="25"></div>
                <input type="text" value="<?php echo Input::old('conteststartdate'); ?>"  id="conteststart" name="conteststartdate" placeholder="Contest Start Date" class="radius pfix_mar" readonly />
                </p>
                @if(isset($er_data['conteststartdate']))
                <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['conteststartdate'] }}</p>
                @endif

                <p>
                <div class="inp_pfix"><img src="{{ URL::to('assets/inner/img/date_icons.png') }}" width="25" height="25"></div>
                <input type="text" value="<?php echo Input::old('contestenddate'); ?>" id="contestend" name="contestenddate" placeholder="Contest End Date" value="" class="radius pfix_mar" readonly />
                </p>	
                @if(isset($er_data['contestenddate']))
                <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['contestenddate'] }}</p>
                @endif
            </legend>	
            <legend class="radius"><div class="leg_head"><span id="txt_votingschedule">Voting Schedule</span></div>

                <p>
                <div class="inp_pfix"><img src="{{ URL::to('assets/inner/img/date_icons.png') }}" width="25" height="25"></div>
                <input type="text" value="<?php echo Input::old('votingstartdate'); ?>" id="votingstart" name="votingstartdate" placeholder="Voting Start Date" value="" class="radius pfix_mar" readonly />
                </p>
                @if(isset($er_data['votingstartdate']))
                <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['votingstartdate'] }}</p>
                @endif

                <p>
                <div class="inp_pfix"><img src="{{ URL::to('assets/inner/img/date_icons.png') }}" width="25" height="25"></div>
                <input type="text" value="<?php echo Input::old('votingenddate'); ?>" id="votingend" name="votingenddate" placeholder="Voting End Date" value="" class="radius pfix_mar" readonly />
                </p> 
                @if(isset($er_data['votingenddate']))
                <p class="alert" style="text-align:left;margin-left:60px;color:red;">{{ $er_data['votingenddate'] }}</p>
                @endif 
            </legend>

            <?php if (Auth::User()->ID == 1) { ?>
                <legend class="radius" style="height:200px;"><div class="leg_head"><span id="txt_sponsorinfo">Sponsor Info</span></div>
                    <p>
                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/sponsor_icons.png')}}" width="25" height="25"></div>
                    <input type="text" id="sponsorname" name="sponsorname" placeholder="Sponsor Name" title="Sponsor Name" value="" class="radius pfix_mar" />
                    </p>

                    <div class="img_pfix mb_upimg"><img src="{{ URL::to('assets/inner/images/avator.png') }}" width="45" height="45" class="roundedimg roundedimgsopn"></div> 
                    <p>
                        <label class="myLabel">
                            <input type="file" class="sponsorimgfile" name="sponsorphoto" />
                            <span>Upload Sponsor Image</span>
                        </label>
                    </p>	



                </legend> <?php } ?>      
        </div>    
        <div class="clrscr"></div>
        <div class="loginbox">
            <p><center>
                <button class="radius martop_10" name="client_login"><span id="txt_createcontest" class="txt_createcontest">Create Contest</span></button>
            </center></p> 
        </div>
    </form>
</div>
<div class="clrscr"></div>
<div class="ddwidth">
    @stop