@extends('header.header')
<!-- Edit user profile page -->
<?php
$assets_path = "assets/inner/";
$showdateformat = Auth::User()->dateformat;
?>
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
  
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript">
$(document).ready(
    function () {
	var dateformat ='<?php echo $showdateformat; ?>';
        $("#datepicker").datepicker({
            changeMonth: true, //this option for allowing user to select month
            changeYear: true, //this option for allowing user to select from year range
            dateFormat: dateformat,
        });
    }
);
</script> 
<link rel="stylesheet" href="{{ URL::to('assets/inner/css/easy-responsive-tabs.css') }}">

@stop
@section('body')

{{ Form::hidden('pagename','edit_profile', array('id'=> 'pagename')) }}
<?php
if (Session::has('er_data')) {
    $er_data = Session::get('er_data');
    //print_r($er_data);
}
if (Session::has('old_data')) {
    $old_data = Session::get('old_data');
    //print_r($old_data);
}
?>
<?php
//$user_id = Auth::user()->ID;
$user_id = $user_id;

$profileData = ProfileModel::where('ID', $user_id)->first();
$interestList = InterestCategoryModel::where('status', 1)->lists('Interest_name', 'Interest_id');
$userInterest = userinterestModel::where('user_id', $user_id)->lists('interest_id');
$timezoneList = timezoneDBModel::lists('timezonename', 'timezonevalue');
?>
<div class="tabs-wrapper">
    <form id="editprofile" name="edit_profile_update" enctype="multipart/form-data"  action="<?php echo url() . "/edit_profile_update/" . $user_id; ?>" method="post" class="form_mid">
<div class="editmypro">
<div class="demo">
<div id="tabInfo">
Selected tab: <span class="tabName"></span>
</div>

<div id="verticalTab">
<div class="title-pro"><span id="txt_editmyprofile">Edit Profile</span>
<span class="edt_but back_but"><a onclick="goback()">Back</a></span>
</div>
<ul class="resp-tabs-list">
<li><span id="txt_logininfo">Login Info</span></li>
<li><span id="txt_personalinfo">Personal Info</span></li>
<li><span id="txt_socialinfo">Social Info</span></li>
<li><span id="txt_favorite">Favorite</span></li>
<li><span id="txt_timezone">Time Zone</span></li>
</ul>

<div class="resp-tabs-container">
@if(isset($er_data['Message']))
        <p class="alert" style="color:green;padding:5px;text-align:center;font-size:13px">{{ $er_data['Message'] }}</p>
        @endif
<div><!-- Tab1--> 
	        <div class="loginform loginbox mar1 prof">
            <legend class="radius"><div class="leg_head"><span id="txt_logininfo">Login Info</span></div>			
                <p>
                <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/user_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_username" name="username" placeholder="User Name" title="User Name" value="{{ isset($old_data['username'])?$old_data['username']:$profileData['username'] }}" <?php if(Auth::user()->ID!=1){ if (Auth::user()->ID != $user_id) {
            echo "readonly";
        } }?>  class="radius pfix_mar" />
                </p>
                @if(isset($er_data['username']))
                <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['username'] }}</p>
                @endif

<?php if (Auth::user()->ID == $user_id) { ?>
                    <p>
                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/pass_icons.png') }}" width="25" height="25"></div>
                    <input type="password" id="pch_password" name="password" placeholder="Password" title="Password" class="radius pfix_mar" />
                    </p>

                    <p>
                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/pass_icons.png') }}" width="25" height="25"></div>
                    <input type="password" id="pch_confirmpassword" name="password_confirmation" placeholder="Confirm Password" title="Confirm Password" class="radius pfix_mar" />
                    </p> 
                    @if(isset($er_data['password']))
                    <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['password'] }}</p>
                    @endif
<?php } ?>
            </legend>
        </div>

</div><!-- Tab1 end -->
<div><!-- Tab2--> 
	        <div class="loginform loginbox mar1 prof">
			            <legend class="radius"><div class="leg_head"><span id="txt_personalinfo">Personal Info</span></div>
                <p>
                <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/user_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_firstname" name="firstname" placeholder="First Name" title="First Name" value="{{ isset($old_data['firstname'])?$old_data['firstname']:$profileData['firstname'] }}" class="radius pfix_mar" />
                </p>    

                <p>
                <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/user_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_lastname" name="lastname" placeholder="Last Name" title="Last Name" value="{{ isset($old_data['lastname'])?$old_data['lastname']:$profileData['lastname'] }}" class="radius pfix_mar" />
                </p>

                <p>
                <div class="img_pfix"><img src="<?php if ($profileData['profilepicture'] != "") {
    echo URL::to('public/assets/upload/profile/' . $profileData['profilepicture']);
} else {
    echo URL::to($assets_path . 'img/user_default_photo.png');
} ?>" width="45" height="45" class="roundedimg"></div>
            <!--<input type="file" name="profilepicture" value="Upload New Image" class="inp_file" />-->
                <label class="myLabel">
                    <script>function setchangeimg(val) {
                            $('#txt_changeprofileimage').html(val);
                        }
                        $(document).on("change", "#groupimage", function () {
                            console.log("The text has been changed.");
                            var file = this.files[0];
                            var imagefile = file.type;
                            var match = ["image/jpeg", "image/png", "image/jpg"];
                            if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2])))
                            {
                                $('.imgblink!').attr('src', 'noimage.png');
                                return false;
                            }
                            else
                            {
                                var reader = new FileReader();
                                reader.onload = imageIsLoaded;
                                reader.readAsDataURL(this.files[0]);
                            }
                            $("#txt_changeprofileimage").html(file.name);
                        });

                        function imageIsLoaded(e) {

                            var image = new Image();
                            image.src = e.target.result;
                            image.onload = function () {
                                $(".roundedimg").attr('src', e.target.result);
                            }
                        }


                    </script>
                    <input type="file" name="profilepicture" id="groupimage" title="Change Profile Image" onchange="setchangeimg(this.value)"/>
                    <span id="txt_changeprofileimage">Change Profile Image</span>
                </label>
                </p>

                <p>
                <div class="inp_pfix aft_up_mar"><img src="{{ URL::to($assets_path.'img/phone_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_mobile" name="mobile" placeholder="Phone" title="Mobile" value="{{ isset($old_data['mobile'])?$old_data['mobile']:$profileData['mobile'] }}" class="radius pfix_mar" />
                </p>

                <p>
                <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/email_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_email" name="email" placeholder="Email" title="Email" value="{{ isset($old_data['email'])?$old_data['email']:$profileData['email'] }}" <?php if(Auth::user()->ID!=1){  if (Auth::user()->ID != $user_id) { echo "readonly"; } } ?> class="radius pfix_mar" />
                </p>
                @if(isset($er_data['email']))
                <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['email'] }}</p>
                @endif 
                <div class="rdogrp">
                    <label><strong><span id="txt_gender">Gender</span></strong></label>
                    <!--<div class="mb_brk"></div>-->
                    <input type="radio" id="gr1" name="gender" value="m" <?php echo ($profileData['gender'] == 'm' || $profileData['gender'] == '') ? "checked" : ""; ?>/>
                    <label for="gr1" id="txt_male">Male</label>
                    <input type="radio" id="gr2"  name="gender" value="f" <?php echo ($profileData['gender'] == 'f') ? "checked" : ""; ?>/>
                    <label for="gr2" id="txt_female">Female</label>
                    <!--<input type="radio" id="gr3" name="gender" value="o" />
                    <label for="gr3" id="txt_others" class="txt_others">Others</label>-->
                </div>

                <p>
                <div class="inp_pfix m-mtop"><!-- style="margin-top:33px !important"--><img src="{{ URL::to($assets_path.'img/date_icons.png') }}" width="25" height="25"></div>
				<?php 

				$showdateformat;
				$dobdata = explode("-",$profileData['dateofbirth']);
				if($showdateformat=='dd/mm/yy'){
				$newdob = $dobdata[2].'/'.$dobdata[1].'/'.$dobdata[0];
				}else{
				$newdob = $dobdata[1].'/'.$dobdata[2].'/'.$dobdata[0];
				}
				//$profileData['dateofbirth']= $profileData['dateofbirth'];
				
				
	   

				?>
                <input type="text" id="datepicker" name="dateofbirth" placeholder="Date of Birth" title="Date of Birth" readonly value="{{ isset($old_data['dateofbirth'])?$old_data['dateofbirth']:$newdob }}" class="radius pfix_mar" />
                </p>
                @if(isset($er_data['dateofbirth']))
                <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['dateofbirth'] }}</p>
                @endif    
                <p>
                <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/location_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_hometown" name="hometown" placeholder="Home Town" title="Home Town" value="{{ isset($old_data['hometown'])?$old_data['hometown']:$profileData['hometown'] }}" class="radius pfix_mar" />
                </p>    

                <p>
                <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/school_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_school" name="school" placeholder="School" title="School" value="{{ isset($old_data['school'])?$old_data['school']:$profileData['school'] }}" class="radius pfix_mar" />
                </p>

                <p>
                <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/occupations_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_occupation" name="occupation" placeholder="Occupation / Profession" title="Occupation / Profession" value="{{ isset($old_data['occupation'])?$old_data['occupation']:$profileData['occupation'] }}" class="radius pfix_mar" />
                </p>

                <div class="clearfix"></div>    
                <div class="rdogrp">
                    <label><strong><span id="txt_maritalstatus">Marital Status</span></strong></label>
                    <!--<div class="mb_brk"></div>-->
                    <input type="radio" id="ms1" name="maritalstatus" value="0" <?php echo ($profileData['maritalstatus'] == 0) ? "checked" : ""; ?>>
                    <label for="ms1" id="txt_single">Single</label>
                    <input type="radio" id="ms2" name="maritalstatus" value="1" <?php echo ($profileData['maritalstatus'] == 1) ? "checked" : ""; ?>>
                    <label for="ms2" id="txt_married">Married</label>
                    <!--<input type="radio" id="ms3" name="maritalstatus" value="2"<?php echo ($profileData['maritalstatus'] == 2) ? "checked" : ""; ?>>
                    <label for="ms3" id="txt_others" class="txt_others">Others</label>-->
                </div>
                <div class="clearfix"></div>

                <p>
                <div class="inp_pfix m-mtop"><!-- style="margin-top:33px !important"--><img src="{{ URL::to($assets_path.'img/kids_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_noofkids" name="noofkids" placeholder="No of Kids" title="No of Kids" value="{{ isset($old_data['noofkids'])?$old_data['noofkids']:$profileData['noofkids'] }}" class="radius pfix_mar" />
                </p>
            </legend> 
			
			</div>
</div><!-- Tab2 end -->

<div><!-- Tab3--> 
	        <div class="loginform loginbox mar1 prof">
			<legend class="radius"><div class="leg_head"><span id="txt_socialinfo">Social Info</span></div>
                <p>
                <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/facebook_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_facebookpage" name="facebookpage" placeholder="Facebook Page" title="Facebook Page" value="{{ isset($old_data['facebookpage'])?$old_data['facebookpage']:$profileData['facebookpage'] }}" class="radius pfix_mar" />
                </p>

                <p>
                <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/twitter_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_twitterpage" name="twitterpage" placeholder="Twitter Page"  title="Twitter Page" value="{{ isset($old_data['twitterpage'])?$old_data['twitterpage']:$profileData['twitterpage'] }}" class="radius pfix_mar" />
                </p>  

                <p>
                <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/instagram_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_instagrampage" name="instagrampage" placeholder="Instagram Page" title="Instagram Page" value="{{ isset($old_data['instagrampage'])?$old_data['instagrampage']:$profileData['instagrampage'] }}" class="radius pfix_mar" />
                </p>
            </legend>
			
			</div>
</div><!-- Tab3 end -->
<div><!-- Tab4--> 
	        <div class="loginform loginbox mar1 prof">
			 <legend class="radius"><div class="leg_head"><span id="txt_favorite">Favorite</span></div>
                <p>
                <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/holiday_icons.png') }}" width="25" height="25"></div>
                <input type="text" id="pch_favholidayspot" name="favoriteholidayspot" placeholder="Favorite Holiday Spot" title="Favorite Holiday Spot" value="{{ isset($old_data['favoriteholidayspot'])?$old_data['favoriteholidayspot']:$profileData['favoriteholidayspot'] }}" class="radius pfix_mar" />
                </p>

                <p>
                <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/interest_icons.png') }}" width="25" height="25"></div>
            <!--<input type="text" id="interest" name="interest" placeholder="Select Interest" value="" class="radius pfix_mar" />-->
<?php
//print_r($userInterest);
?>
                {{ Form::select('interest[]', $interestList,$userInterest, array('class'=>'SlectBox testsel radius','multiple'=>'multiple','placeholder'=>'Select Interest','title'=>'Select Interest','onchange'=>'console.log($(this).children(":selected").length)','style'=>'width:97%')) }}
                </p>
            </legend>
			
			</div>
</div><!-- Tab4 end -->
<div><!-- Tab5 -->
	<div class="loginform loginbox mar1 prof">
	<legend class="radius"><div class="leg_head txt_timezone">Time Zone</div>
                <p>
                <div class="inp_pfix mb_sel_mt"><img src="{{ URL::to('/assets/inner/img/timezone_icons.png') }}" width="25" height="25"></div>
                {{ Form::select('timezone', array("Select Time Zone")+$timezoneList,isset($old_data['timezone'])?$old_data['timezone']:$profileData['timezone'], array('class'=>'SlectBox testsel radius','placeholder'=>'Select Timezone','title'=>'Select Timezone','onchange'=>'console.log($(this).children(":selected").length)')) }}
                </p>
                @if(isset($er_data['timezone']))
                <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['timezone'] }}</p>
                @endif 
				<p><div class="leg_head">Date format</div></p>
				<p>
				<div class="inp_pfix mb_sel_mt"><img src="{{ URL::to('/assets/inner/img/timezone_icons.png') }}" width="25" height="25"></div>
				<?php 
				$dateformat = array("mm/dd/yy"=>"mm/dd/yy", "dd/mm/yy"=>"dd/mm/yy");
				
				//print_r $old_data['dateformat'];
				
				?>
				{{ Form::select('dateformat',$dateformat,isset($old_data['dateformat'])?$old_data['dateformat']:$profileData['dateformat'], array('class'=>'SlectBox testsel radius','placeholder'=>'Select Date format','title'=>'Select Dateformat')) }}
				
				</p>

            </legend>
			</div>
			</div><!-- Tab5 end -->

</div>
</div>
</div></div>

<!--<a href="#" onclick="goback()" ><label for="tab6" id="txt_editprofile">Back</label></a>-->

 
 <div class="clrscr"></div>

        <div class="loginbox">
            <p><center>
                <button class="radius martop_10" name="update_profile"><span id="txt_updateprofile">Update Profile</span></button>					
            </center></p> 
        </div>
    </form>
</div>
<!-- tab script-->

<script src="{{ URL::to('assets/inner/js/easy-responsive-tabs.js') }}"></script> 

<script>
$(document).ready(function () {
$('#horizontalTab').easyResponsiveTabs({
type: 'default', //Types: default, vertical, accordion           
width: 'auto', //auto or any width like 600px
fit: true,   // 100% fit in a container
closed: 'accordion', // Start closed if in accordion view
activate: function(event) { // Callback function if tab is switched
var $tab = $(this);
var $info = $('#tabInfo');
var $name = $('span', $info);
$name.text($tab.text());
$info.show();
}
});
$('#verticalTab').easyResponsiveTabs({
type: 'vertical',
width: 'auto',
fit: true
});
});
</script>
<!--tab script ends-->
@endsection