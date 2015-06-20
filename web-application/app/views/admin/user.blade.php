@extends('header.header')
<!-- This shows the user list -->
<?php
$assets_path = "assets/inner/";
?>
@section('includes')

<link rel="stylesheet" media="all" type="text/css" href="{{ URL::to('assets/inner/css/jquery-ui.css') }}" />
<link rel="stylesheet" media="all" type="text/css" href="{{ URL::to('assets/inner/css/jquery-ui-timepicker-addon.css') }}" />    
<script src="{{ URL::to('assets/inner/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/jquery-ui-timepicker-addon.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/jquery-ui-sliderAccess.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/date_time_script.js') }}"></script>
<link type="text/css" rel="stylesheet" href="{{ URL::to('assets/inner/js/datatbl/demo_table.css') }}">
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.dataTables.js') }}"></script>


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
            $("#datepicker").datepicker({
                changeMonth: true, //this option for allowing user to select month
                changeYear: true, //this option for allowing user to select from year range
                dateFormat: 'yy-mm-dd'
            });
        }
);


$(document).ready(function (e) {
    $('#ct1').click(function () {
        $('.rdogrp').show();
    });
    $('#ct2').click(function () {
        $('.rdogrp1').hide();
    });

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
        $("#txt_uploadgroupimage").html(file.name);
    });
////// For tab 2 ////////////////////
    jQuery('#dd_group_list').dataTable({
        "bPaginate": true,
        "sPaginationType": "full_numbers",
        "iDisplayLength": 10,
        "sPageButton": "paginate_button",
        "bFilter": false
    });

});

function imageIsLoaded(e) {

    var image = new Image();
    image.src = e.target.result;
    image.onload = function () {
        $(".roundedimg").attr('src', e.target.result);
    }
}



function updateuploadcontestimg(vals)
{
    $("#txt_uploadgroupimage").html(vals);
}


function showtab() {
    var main_tab = $('#mobileselected').val();
    window.location = "<?php echo url(); ?>/groupresponsive?tabname=" + main_tab;
}

/////////// User events ///////////
function changeactive(userid) {
    var dataString = "userid=" + userid;
    $.ajax({
        type: "post",
        url: 'activeuser',
        data: dataString,
        success: function (data) {
            console.log(data);
            if (data == 1) {

                $('.backclr_' + userid).css('background-color', 'green');
                $('#ajaxmessage').html('Activated successfully');

            } else {
                $('.backclr_' + userid).css('background-color', 'red');
                $('#ajaxmessage').html('Deactivated successfully');
            }
        }
    });
}

function userdelete(userid) {

    var answer = confirm('Are you sure want to delete?');
    if (answer) {
        window.location = "<?php echo url(); ?>/userdelete?userid=" + userid;
    }

}


</script>	

@stop
@section('body')
{{ Form::hidden('pagename','user', array('id'=> 'pagename')) }}

<?php
if (Session::has('tab')) {
    $tab = Session::get('tab');
} else {
    $tab = "userlist";
}
?>
@if(isset($er_data))
<p class="alert" style="color:green;padding:5px;text-align:center;font-size:13px">{{ $er_data }}</p>
@endif
<?php
if (Session::has('er_data')) {
    $er_data = Session::get('er_data');
}
if (Session::has('searcheduser')) {

    $searcheduser = Session::get('searcheduser');
} else {
    $searcheduser = '';
}
?>

<div id="con_grp" class="tabs-wrapper">
    <input type="radio" name="tab" id="tab1" class="tab-head" <?php
    if ($tab == "userlist") {
        echo "checked";
    }
    ?> />
    <label for="tab1">User List</span></label>
    <input type="radio" name="tab" id="tab2" class="tab-head" <?php
    if ($tab == "createuser") {
        echo "checked";
    }
    ?>/>
    <label for="tab2"><span id="submnu_createuser">Add user</span></label>
    <div class="mbblk">
        <select class="radius sel_lang" onchange="showtab()" id="mobileselected">
            <option value="createuser" <?php
                    if ($tab == "createuser") {
                        echo "selected";
                    }
                    ?>>Create Group</option>
            <option value="userlist" <?php
                    if ($tab == "userlist") {
                        echo "selected";
                    }
                    ?>>Group List</option>
        </select>
    </div>

    <div class="tab-body-wrapper">

        <!------ User List---------------------->
        <div id="tab-body-1" class="tab-body">
            @if(isset($er_data['message']))
            <p class="alert" style="color:green;padding:5px;text-align:center;font-size:13px">{{ $er_data['message'] }}</p>
            @endif
            <span class="alert" id="ajaxmessage" style="color:green;padding:5px;text-align:center;font-size:13px"></span>

            <div class="con_hed_blk">
                <div class="group_search">
                    <form name="tab2-search"  action="{{ URL::to('searchuser') }}" method="post" >
                        <div class="mb_group_search" style="vertical-align:top;margin:0; padding:0;">
                            <input type="hidden" name="tab" value="userlist">

                            <input type="text" name="tsearch2" id="tsearch" value="{{ isset($inputs['tsearch2'])?$inputs['tsearch2']:'' }}" class="pch_searchgroup" placeholder="Search User" />
                            <input class="search_btn" type="submit" value="" />
                        </div>
                    </form>
                </div>
            </div>

            <table class="display" cellspacing="0" width="100%" id="dd_group_list">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Name</th>
                        <th>User name</th>
                        <th>Image</th>
                        <th>Email id</th>
                        <th>Mobile</th>
                        <th>contest</th>
                        <th>Active/Inactive</th>
                        <th class="tr_wid_button1" align="center"><span class="txt_view">View</span></th>
                        <th class="tr_wid_button1" align="center"><span class="txt_edit">Edit</span></th>
                        <th class="tr_wid_edit"><span class="txt_delete">Delete<span></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $user_id = Auth::user()->ID;

                                        if ($searcheduser != '') {

                                            $userlist = ProfileModel::select('profilepicture', 'firstname', 'lastname', 'username', 'ID', 'email', 'mobile', 'status')->where('username', 'like', '%' . $searcheduser . '%')->Orwhere('firstname', 'like', '%' . $searcheduser . '%')->get();
                                        } else {
                                            $userlist = ProfileModel::select('profilepicture', 'firstname', 'lastname', 'username', 'ID', 'email', 'mobile', 'status')->get();
                                        }

                                        for ($i = 0; $i < count($userlist); $i++) {
                                            if ($userlist[$i]['ID'] != 1) {
                                                ?>
                                                <tr>
                                                    <td>{{ $i; }} </td>
                                                    <td class="tr_wid_id">{{ $userlist[$i]['firstname'].' '.$userlist[$i]['lastname'] }}</td>
                                                    <td >{{ $userlist[$i]['username'] }}</td>
                                                    <td align="center"><img src="{{ ($userlist[$i]['profilepicture']!='')?(URL::to('public/assets/upload/profile/'.$userlist[$i]['profilepicture'])):(URL::to('assets/inner/images/avator.png')) }}" width="50" height="50"></td>
                                                    <td>{{$userlist[$i]['email']}}</td>
                                                    <td>{{$userlist[$i]['mobile']}}</td>
                                                    <td align="center"><a href="<?php echo url(); ?>/viewusercontest/<?php echo $userlist[$i]['ID']; ?>" class="contest-link"></a></td>
                                                    <td align="center"><a href="#" onclick="changeactive('<?php echo $userlist[$i]['ID']; ?>')" <?php
                                                if ($userlist[$i]['status'] == 1) {
                                                    echo 'style="background-color:green;"';
                                                } else {
                                                    echo 'style="background-color:red;"';
                                                }
                                                ?> class="add-link backclr_<?php echo $userlist[$i]['ID']; ?>"></a></td>
                                                    <td class="tr_wid_button1" align="center"><a href="<?php echo url(); ?>/other_profile/<?php echo $userlist[$i]['ID']; ?>" class="view-link"></a></td>
                                                    <td class="tr_wid_button1" align="center"><?php if (Auth::user()->ID == 1) { ?><a href="<?php echo url(); ?>/edit_profile/<?php echo $userlist[$i]['ID']; ?>" class="edit-link"></a><?php } ?></td>
                                                    <td align="center"><?php if (Auth::user()->ID == 1) { ?><a href="#" class="del-link" onclick="userdelete('<?php echo $userlist[$i]['ID']; ?>')" ></a><?php } ?></td>
                                                </tr>
        <?php
    }
}
?>

                                    </tbody>
                                    </table>   
                                    </div>
                                    <div id="tab-body-2" class="tab-body">


                                        <form id="editprofile" name="edit_profile_update" enctype="multipart/form-data"  action="{{ URL::to('/adduser') }}" method="post" class="form_mid">
                                            @if(isset($Message))
                                            <p class="alert" style="color:green;padding:5px;text-align:center;font-size:13px">{{ $Message }}</p>
                                            @endif
                                            <?php
                                            $user_id = Auth::user()->ID;
                                            $profileData = ProfileModel::where('ID', $user_id)->first();
                                            $interestList = InterestCategoryModel::lists('Interest_name', 'Interest_id');
                                            $userInterest = InterestCategoryModel::lists('Interest_name', 'Interest_id');
                                            //$userInterest=userinterestModel::where('user_id',$user_id)->lists('interest_id');
                                            $timezoneList = timezoneDBModel::lists('timezonename', 'timezonevalue');
                                            if (Session::has('er_data')) {
                                                $er_data = Session::get('er_data');

                                            }
                                            if (Session::has('old_data')) {
                                                $old_data = Session::get('old_data');

                                            }
                                            ?>
                                            <div class="loginform loginbox mar1">

                                                <legend class="radius"><div class="leg_head"><span id="txt_logininfo">Login Info</span></div>

                                                    <p>
                                                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/user_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="pch_username" name="username" placeholder="User Name" title="User Name" value="{{ isset($old_data['username'])?$old_data['username']:'' }}" class="radius pfix_mar" />
                                                    </p>
                                                    @if(isset($er_data['username']))
                                                    <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['username'] }}</p>
                                                    @endif

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
                                                </legend>

                                                <legend class="radius"><div class="leg_head"><span id="txt_socialinfo">Social Info</span></div>
                                                    <p>
                                                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/facebook_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="pch_facebookpage" name="facebookpage" placeholder="Facebook Page" title="Facebook Page" value="{{ isset($old_data['facebookpage'])?$old_data['facebookpage']:'' }}" class="radius pfix_mar" />
                                                    </p>

                                                    <p>
                                                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/twitter_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="pch_twitterpage" name="twitterpage" placeholder="Twitter Page"  title="Twitter Page" value="{{ isset($old_data['twitterpage'])?$old_data['twitterpage']:'' }}" class="radius pfix_mar" />
                                                    </p>  

                                                    <p>
                                                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/instagram_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="pch_instagrampage" name="instagrampage" placeholder="Instagram Page" title="Instagram Page" value="{{ isset($old_data['instagrampage'])?$old_data['instagrampage']:'' }}" class="radius pfix_mar" />
                                                    </p>
                                                </legend>

                                                <legend class="radius"><div class="leg_head"><span id="txt_favorite">Favorite</span></div>
                                                    <p>
                                                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/holiday_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="pch_favholidayspot" name="favoriteholidayspot" placeholder="Favorite Holiday Spot" title="Favorite Holiday Spot" value="{{ isset($old_data['favoriteholidayspot'])?$old_data['favoriteholidayspot']:'' }}" class="radius pfix_mar" />
                                                    </p>

                                                    <p>
                                                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/interest_icons.png') }}" width="25" height="25"></div>

                                                    {{ Form::select('interest[]', $interestList,$userInterest, array('class'=>'SlectBox testsel radius','multiple'=>'multiple','placeholder'=>'Select Interest','title'=>'Select Interest')) }}
                                                    </p>
                                                </legend>
                                            </div>

                                            <div class="loginform loginbox mar2">
                                                <legend class="radius"><div class="leg_head"><span id="txt_personalinfo">Personal Info</span></div>
                                                    <p>
                                                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/user_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="pch_firstname" name="firstname" placeholder="First Name" title="First Name" value="{{ isset($old_data['firstname'])?$old_data['firstname']:'' }}" class="radius pfix_mar" />
                                                    </p>    

                                                    <p>
                                                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/user_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="pch_lastname" name="lastname" placeholder="Last Name" title="Last Name" value="{{ isset($old_data['lastname'])?$old_data['lastname']:'' }}" class="radius pfix_mar" />
                                                    </p>

                                                    <p>
                                                    <div class="img_pfix"><img src="<?php echo URL::to($assets_path . 'img/user_default_photo.png'); ?>" width="45" height="45" class="roundedimg"></div>
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
                                                        <input type="file" name="profilepicture" id="groupimage" title="Upload Profile Image" onchange="setchangeimg(this.value)"/>
                                                        Upload Profile Image
                                                    </label>
                                                    </p>

                                                    <p>
                                                    <div class="inp_pfix aft_up_mar"><img src="{{ URL::to($assets_path.'img/phone_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="pch_mobile" name="mobile" placeholder="Phone" title="Mobile" value="{{ isset($old_data['mobile'])?$old_data['mobile']:'' }}" class="radius pfix_mar" />
                                                    </p>

                                                    <p>
                                                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/email_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="pch_email" name="email" placeholder="Email" title="Email" value="{{ isset($old_data['email'])?$old_data['email']:'' }}" class="radius pfix_mar" />
                                                    </p>
                                                    @if(isset($er_data['email']))
                                                    <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['email'] }}</p>
                                                    @endif 
                                                    <div class="rdogrp">
                                                        <label><strong>Gender</strong></label>

                                                        <input type="radio" id="gr1" name="gender" value="m" checked />
                                                        <label for="gr1" id="txt_male">Male</label>
                                                        <input type="radio" id="gr2"  name="gender" value="f" />
                                                        <label for="gr2" id="txt_female">Female</label>

                                                    </div>

                                                    <p>
                                                    <div class="inp_pfix aft_rdo_mar1"><img src="{{ URL::to($assets_path.'img/date_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="datepicker" name="dateofbirth" placeholder="Date of Birth" title="Date of Birth" value="{{ isset($old_data['dateofbirth'])?$old_data['dateofbirth']:'' }}" class="radius pfix_mar" />
                                                    </p>
                                                    @if(isset($er_data['dateofbirth']))
                                                    <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['dateofbirth'] }}</p>
                                                    @endif    
                                                    <p>
                                                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/location_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="pch_hometown" name="hometown" placeholder="Home Town" title="Home Town" value="{{ isset($old_data['hometown'])?$old_data['hometown']:'' }}" class="radius pfix_mar" />
                                                    </p>    

                                                    <p>
                                                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/school_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="pch_school" name="school" placeholder="School" title="School" value="{{ isset($old_data['school'])?$old_data['school']:'' }}" class="radius pfix_mar" />
                                                    </p>

                                                    <p>
                                                    <div class="inp_pfix"><img src="{{ URL::to($assets_path.'img/occupations_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="pch_occupation" name="occupation" placeholder="Occupation / Profession" title="Occupation / Profession" value="{{ isset($old_data['occupation'])?$old_data['occupation']:'' }}" class="radius pfix_mar" />
                                                    </p>

                                                    <div class="clearfix"></div>    
                                                    <div class="rdogrp">
                                                        <label><strong><span id="txt_maritalstatus">Marital Status</span></strong></label>

                                                        <input type="radio" id="ms1" name="maritalstatus" value="0" checked >
                                                        <label for="ms1" id="txt_single">Single</label>
                                                        <input type="radio" id="ms2" name="maritalstatus" value="1" >
                                                        <label for="ms2" id="txt_married">Married</label>                
                                                        <label for="ms3" id="txt_others" class="txt_others">Others</label>
                                                    </div>
                                                    <div class="clearfix"></div>

                                                    <p>
                                                    <div class="inp_pfix aft_rdo_mar1"><img src="{{ URL::to($assets_path.'img/kids_icons.png') }}" width="25" height="25"></div>
                                                    <input type="text" id="pch_noofkids" name="noofkids" placeholder="No of Kids" title="No of Kids" value="{{ isset($old_data['noofkids'])?$old_data['noofkids']:'' }}" class="radius pfix_mar" />
                                                    </p>
                                                </legend>    
                                                <legend class="radius"><div class="leg_head txt_timezone">Time Zone</div>
                                                    <p>
                                                    <div class="inp_pfix mb_sel_mt"><img src="{{ URL::to('/assets/inner/img/timezone_icons.png') }}" width="25" height="25"></div>
                                                    {{ Form::select('timezone', array("Select Time Zone")+$timezoneList,isset($old_data['timezone'])?$old_data['timezone']:'', array('class'=>'SlectBox testsel radius','placeholder'=>'Select Timezone','title'=>'Select Timezone','onchange'=>'console.log($(this).children(":selected").length)')) }}
                                                    </p>
                                                    </p>
                                                    @if(isset($er_data['timezone']))
                                                    <p class="alert" style="text-align:left;margin-left:55px;color:red;">{{ $er_data['timezone'] }}</p>
                                                    @endif    
                                                    <p>

                                                </legend>
                                            </div>                             


                                            <div class="clrscr"></div>

                                            <div class="loginbox">
                                                <p><center>
                                                    <button class="radius martop_10" name="update_profile"><span id="txt_updateprofile">Add user</span></button>

                                                </center></p> 
                                            </div>
                                        </form>
                                    </div>


                                    <div id="tab-body-3" class="tab-body">
                                        <div class="sharebox fullwidth">
                                            <h1>Share the Group with</h1>
                                            <br>

                                            <center>
                                                <style>
                                                    .stButton .stLarge {
                                                        display: inline-block;
                                                        width: 300px;
                                                        height:35px;
                                                        position: relative;
                                                        color:#fff;
                                                        font-size:16px;
                                                        padding:10px;
                                                        text-align :left;
                                                        -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px;
                                                    }
                                                    .st_facebook_large .stButton .stLarge {
                                                        background: #314984 url(../assets/inner/img/share_facebook.png) no-repeat 5px -2px !important;
                                                    }

                                                    .st_facebook_large .stButton .stLarge:before {
                                                        content:'Facebook';
                                                        margin-left:30px;
                                                    }

                                                    .st_email_large .stButton .stLarge {
                                                        background: #9BA2AA url(../assets/inner/img/share_email.png) no-repeat 5px -2px !important;
                                                    }
                                                    .st_email_large .stButton .stLarge:before {
                                                        content:'Email';
                                                        margin-left:30px;
                                                    }

                                                    .st_twitter_large .stButton .stLarge {
                                                        background: #00ACED url(../assets/inner/img/share_twitter.png) no-repeat 5px -2px !important;
                                                    }
                                                    .st_twitter_large .stButton .stLarge:before {
                                                        content:'Twitter';
                                                        margin-left:30px;
                                                    }

                                                    .st_linkedin_large .stButton .stLarge {
                                                        background: #007BB6 url(../assets/inner/img/share_linkedin.png) no-repeat 5px -2px !important;
                                                    }
                                                    .st_linkedin_large .stButton .stLarge:before {
                                                        content:'LinkedIn';
                                                        margin-left:30px;
                                                    }
                                                    .st_tumblr_large .stButton .stLarge {
                                                        background: #3E5976 url(../assets/inner/img/share_tumblr.png) no-repeat 5px -2px !important;
                                                    }
                                                    .st_tumblr_large .stButton .stLarge:before {
                                                        content:'Tumblr';
                                                        margin-left:30px;
                                                    }
                                                </style>
                                                <p><span class='st_facebook_large' displayText='Facebook'></span></p><br>
                                                <p><span class='st_twitter_large' displayText='Tweet'></span></p><br>
                                                <p><span class='st_tumblr_large' displayText='Tumblr'></span></p><br>
                                                
                                                <p><span class='st_email_large' displayText='Email'></span></p>
                                                
                                            </center>
                                        </div>
                                    </div>

                                    </div>
                                    </div>
                                    <div class="clear"></div>
                                    @stop