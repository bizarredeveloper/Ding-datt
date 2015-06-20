@extends('header.header')
<?php
$assets_path = "assets/inner/";
?>
@section('includes')

<link type="text/css" rel="stylesheet" href="{{ URL::to('assets/inner/js/datatbl/demo_table.css') }}">
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.dataTables.js') }}"></script>
<link rel="stylesheet" media="all" type="text/css" href="{{ URL::to('assets/inner/css/jquery-ui.css') }}" />
<link rel="stylesheet" media="all" type="text/css" href="{{ URL::to('assets/inner/css/jquery-ui-timepicker-addon.css') }}" />    
<link type="text/css" rel="stylesheet" href="{{ URL::to('assets/inner/js/datatbl/demo_table.css') }}">
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.dataTables.js') }}"></script>


<script type="text/javascript">
$(document).ready(function () {

    jQuery('#dd_profile_following,#dd_profile_followers,#dd_profile_group').dataTable({
        "bPaginate": true,
        "sPaginationType": "full_numbers",
        "iDisplayLength": 10,
        "sPageButton": "paginate_button"
    });
});

function responsive_menu(profile_id) {
    var main_tab = $('#mobileselected').val();
    window.location = "<?php echo url(); ?>/otherprofileresponsive?profile_id=" + profile_id + "&tabname=" + main_tab;
}
function responsive_sub_menu(profile_id)
{
    var main_tab = $('#mobilesubmenuselected').val();
    window.location = "<?php echo url(); ?>/edit_profile";
}

function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

</script>
@stop
@section('body')
{{ Form::hidden('pagename','other_profile', array('id'=> 'pagename')) }}
<?php
if (Session::has('tab')) {
    $tab = Session::get('tab');
} else {
    $tab = "profilinfo";
}
if (Session::has('Message')) {
    $Message = Session::get('Message');
}
?>
<!--menu ends-->
<div class="tabs-wrapper">
    <!--<div class="tab_desk">-->
    <input type="radio" name="tab" id="tab1" class="tab-head" checked="checked" <?php if ($tab == "profilinfo") {
    echo "checked";
} ?>  />
    <label for="tab1"><span id="txt_submenu_profile">Profile Info</span></label>
    <input type="radio" name="tab" id="tab2" class="tab-head" <?php if ($tab == "history") {
    echo "checked";
} ?> />
    <label for="tab2"><span id="txt_submenu_history">History</span></label>
    <input type="radio" name="tab" id="tab3" class="tab-head" <?php if ($tab == "Followers") {
    echo "checked";
} ?> />
    <label for="tab3"><span id="txt_followers">Followers</span></label>
    <input type="radio" name="tab" id="tab4" class="tab-head" <?php if ($tab == "following") {
    echo "checked";
} ?> />
    <label for="tab4"><span id="txt_following">Following</span></label>
    <input type="radio" name="tab" id="tab5" class="tab-head" <?php if ($tab == "group") {
    echo "checked";
} ?> />
    <label for="tab5"><span class="mnu_group">Group</span></label>
    <!--</div>-->

    <div id="subtab_div" class="con_cat_right mbnone" >
    </div>

    <div class="con_cat_right">
        <?php
        if ($profileid == Auth::user()->ID) {
            $authuserid = Auth::user()->ID;
            ?>
            <label for="tab6" id="txt_editprofile"><a href="<?php echo url() . '/edit_profile/' . $authuserid; ?>"><span id="txt_submenu_editprofile">Edit Profile</span></a></label>

    <?php
}
?>
        <label for="tab6" id="txt_editprofile"><a href="#" onclick="goback()" >Back</a></label>
    </div>

    <div class="mbblk">
        <select class="radius sel_lang" id="mobileselected"  onchange="responsive_menu('<?php echo $profileid; ?>')">
            <option class="txt_submenu_profile" value="profilinfo" <?php if ($tab == "profilinfo") {
    echo "selected";
} ?>>My Profile</option>
            <option class="txt_submenu_history" value="history" <?php if ($tab == "history") {
    echo "selected";
} ?>>History</option>
            <option class="txt_followers" value="follower" <?php if ($tab == "follower") {
            echo "selected";
        } ?>>Followers</option>
            <option class="txt_following" value="following" <?php if ($tab == "following") {
            echo "selected";
        } ?>>Following</option>
            <option class="mnu_group" value="group" <?php if ($tab == "group") {
            echo "selected";
        } ?>>Group</option>
        </select>	

                <?php if ($profileid == Auth::user()->ID) { ?> 
            <select class="radius sel_lang" id="mobilesubmenuselected"  onclick="responsive_sub_menu('<?php echo $profileid; ?>')">
                <option class="txt_submenu_editprofile" value="editprofil" <?php if ($tab == "editprofil") {
                    echo "selected";
                } ?>>Edit Profile</option> 
            </select>
                <?php } ?>

    </div>

    <div class="tab-body-wrapper"> 

        <!--- Personal Info--->
        <div id="tab-body-1" class="tab-body">
            <div id="p">


                        <?php
                        $followercnt = followModel::where('userid', Auth::user()->ID)->where('followerid', $profileid)->get()->count();
                        if (Auth::user()->ID != $profileid) {
                            ?>


                    <div style="float:left;"><?php if ($followercnt) { ?><img src="{{ URL::to('/assets/inner/img/bell_symbol.png') }}" title="Following" ><?php } else { ?><a href="<?php echo url(); ?>/putfollowinotherprofile?followerid=<?php echo $profileid; ?>" ><input type="button" id="follow" name="" value="Follow" title="Follow" style="cursor:pointer;"  class="follow_btn" /></a></br><?php } ?></div>	

<?php
}

if (Session::has('Massage'))
    $inv_suc_message = Session::get('Massage');
?><div id="inv_success" style="color:green;text-align:center;font-size:14px"><?php if (Session::has('Massage')) echo $inv_suc_message; ?> </div>	
                <div class="clrscr"></div>
                <form id="editprofile" action="" method="post" class="form_mid">
                    <div class="loginform loginbox mar1">
<?php
$profiledetail = ProfileModel::where('ID', $profileid)->get();
$timezoneList = timezoneDBModel::lists('timezonename', 'timezonevalue');
?>
                        <legend class="radius"><div class="leg_head"><span id="txt_personalinfo">Personal Info</span></div>

                            <div class="personal_info">

                                @if($profiledetail[0]['profilepicture']!='')
                                <div><img src="{{ ($profiledetail[0]['profilepicture']!='')?(URL::to('public/assets/upload/profile/'.$profiledetail[0]['profilepicture'])):(URL::to('assets/inner/images/avator.png')) }}" width="100" height="100" class="roundedimg brd_grn"></div>
                                @endif
                                @if($profiledetail[0]['profilepicture']=='')
                                <div><img src="{{ URL::to('assets/inner/images/avator.png') }}" width="100" height="100" class="roundedimg brd_grn"></div>
                                @endif

                                <div class="per_det">
                                    <div><strong><span id="pch_firstname">First Name:</span></strong> {{ $profiledetail[0]['firstname'] }}</div>
                                    <div><strong><span id="pch_lastname">Last Name:</span></strong> {{ $profiledetail[0]['lastname'] }}</div>
                                    <div><strong><span id="txt_username">User Name:</span></strong> {{ $profiledetail[0]['username'] }}</div>
                                </div>
                            </div>

                            <div <?php if (Auth::user()->ID == 1 || Auth::user()->ID == $profileid) {
    
} else { ?> style="visibility:hidden; height:1px;" <?php } ?>>
                                <div class="inp_pfix aft_up_mar"><img src="{{ URL::to('/assets/inner/img/phone_icons.png') }}" width="25" height="25"></div>
                                <input type="text" id="pch_mobile" name="phone" placeholder="Phone" disabled  title="Phone" value="{{ $profiledetail[0]['mobile'] }}" class="radius pfix_mar" />
                            </div>

                            <p>
                            <div class="inp_pfix"><img src="{{ URL::to('/assets/inner/img/email_icons.png') }}" width="25" height="25"></div>
                            <input type="text" id="pch_email" name="email" disabled placeholder="Email" title="Email" value="{{ $profiledetail[0]['email']; }}" class="radius pfix_mar" />
                            </p>

                            <div class="rdogrp">
                                <label><strong><span id="txt_gender">Gender</span></strong></label>
                                <input type="radio" id="gr1" name="gr" value="m" disabled <?php echo ($profiledetail[0]['gender'] == 'm' || $profiledetail[0]['gender'] == '') ? "checked" : ""; ?> >
                                <label for="gr1" id="txt_male">Male</label>
                                <input type="radio" id="gr2" name="gr"value="f" disabled <?php echo ($profiledetail[0]['gender'] == 'f') ? "checked" : ""; ?>>
                                <label for="gr2" id="txt_female">Female</label>
                            </div>

                            <p>

<?php
$originalDate = $profiledetail[0]['dateofbirth'];
$newDate = date("d-m-Y", strtotime($originalDate));
?>
                            <div class="inp_pfix aft_rdo_mar1"><img src="{{ URL::to('/assets/inner/img/date_icons.png') }}" width="25" height="25"></div>
                            <input type="text" id="datepicker" disabled name="dob" placeholder="Date of Birth" title="Date of Birth" value="{{ $newDate  }}" class="radius pfix_mar" />
                            </p>

                            <p>
                            <div class="inp_pfix "><img src="{{ URL::to('/assets/inner/img/location_icons.png') }}" width="25" height="25"></div>
                            <input type="text" id="pch_hometown" disabled name="hometown" placeholder="Home Town" title="Home Town" value="{{ $profiledetail[0]['hometown']  }}" class="radius pfix_mar" />
                            </p>

                            <p>
                            <div class="inp_pfix"><img src="{{ URL::to('/assets/inner/img/school_icons.png') }}" width="25" height="25"></div>
                            <input type="text" id="pch_school" disabled name="school" placeholder="School" title="School" value="{{ $profiledetail[0]['school']  }}" class="radius pfix_mar" />
                            </p>

                            <p>
                            <div class="inp_pfix"><img src="{{ URL::to('/assets/inner/img/occupations_icons.png') }}" width="25" height="25"></div>
                            <input type="text" id="pch_occupation" disabled name="occupation" placeholder="Occupation / Profession" title="Occupation" value="{{ $profiledetail[0]['occupation']  }}" class="radius pfix_mar" />
                            </p>


                            <div class="clearfix"></div>

                            <div class="rdogrp">
                                <label><strong><span id="txt_maritalstatus">Marital Status</span></strong></label>
                                <!--<div class="mb_brk"></div>-->
                                <input type="radio" id="ms1" name="ms" disabled value="0" <?php echo ($profiledetail[0]['maritalstatus'] == 0) ? "checked" : ""; ?> >
                                <label for="ms1" id="txt_single">Single</label>
                                <input type="radio" id="ms2" name="ms"value="1" disabled <?php echo ($profiledetail[0]['maritalstatus'] == 1) ? "checked" : ""; ?>>
                                <label for="ms2" id="txt_married">Married</label>
                            </div>

                            <div class="clearfix"></div>

                            <p>
                            <div class="inp_pfix aft_rdo_mar1"><img src="{{ URL::to('/assets/inner/img/kids_icons.png') }}" width="25" height="25"></div>
                            <input type="text" id="noofkids" disabled name="noofkids" placeholder="No of Kids" title="No of Kids" value="{{ $profiledetail[0]['noofkids']  }}" class="radius pfix_mar" />
                            </p>

                        </legend>

                    </div>

                    <div class="loginform loginbox mar2">


                        <legend class="radius"><div class="leg_head"><span id="txt_socialinfo">Social Info</span></div>
                            <p>
                            <div class="inp_pfix"><img src="{{ URL::to('/assets/inner/img/facebook_icons.png') }}" width="25" height="25"></div>
                            <input type="text" id="pch_facebookpage" disabled name="facebookpage" placeholder="Facebook Page" title="Facebook Page" value="{{ $profiledetail[0]['facebookpage']  }}" class="radius pfix_mar" />
                            </p>

                            <p>
                            <div class="inp_pfix"><img src="{{ URL::to('/assets/inner/img/twitter_icons.png') }}" width="25" height="25"></div>
                            <input type="text" id="pch_twitterpage" disabled name="twitterpage" placeholder="Twitter Page" title="Twitter Page" value="{{ $profiledetail[0]['twitterpage']  }}" class="radius pfix_mar" />
                            </p>

                            <p>
                            <div class="inp_pfix"><img src="{{ URL::to('/assets/inner/img/instagram_icons.png') }}" width="25" height="25"></div>
                            <input type="text" disabled id="pch_instagrampage" name="instagrampage" placeholder="Instagram Page" title="Instagram Page" value="{{ $profiledetail[0]['instagrampage']  }}" class="radius pfix_mar" />
                            </p>
                        </legend>

                        <legend class="radius"><div class="leg_head"><span id="txt_favorite">Favorite</span></div>

                            <p>
                            <div class="inp_pfix"><img src="{{ URL::to('/assets/inner/img/holiday_icons.png') }}" width="25" height="25"></div>
                            <input type="text" disabled id="pch_favholidayspot" name="favholidayspot" placeholder="Favorite Holiday Spot" title="Favorite Holiday Spot" value="{{ $profiledetail[0]['favoriteholidayspot']  }}" class="radius pfix_mar" />
                            </p>

                            <p>
                            <div class="inp_pfix mb_sel_mt"><img src="{{ URL::to('/assets/inner/img/interest_icons.png') }}" width="25" height="25"></div>

<?php
$interestList = InterestCategoryModel::lists('Interest_name', 'Interest_id');
$userInterest = userinterestModel::where('user_id', $profileid)->lists('interest_id');
?>
                            {{ Form::select('interest[]', $interestList,$userInterest, array('class'=>'SlectBox testsel radius','multiple'=>'multiple','placeholder'=>'Select Interest','title'=>'Select Interest','disabled' => 'disabled','onchange'=>'console.log($(this).children(":selected").length)')) }}

                            </p>

                        </legend>

                        <legend class="radius"><div class="leg_head" id="txt_timezone"  >Time Zone</div>

                            <p>
                            <div class="inp_pfix mb_sel_mt"><img src="{{ URL::to('/assets/inner/img/timezone_icons.png') }}" width="25" height="25"></div>
                            {{ Form::select('timezone', array("Select Time Zone")+$timezoneList,isset($old_data['timezone'])?$old_data['timezone']:$profiledetail[0]['timezone'], array('class'=>'SlectBox testsel radius','placeholder'=>'Select Timezone','title'=>'Select Timezone', 'disabled' => 'disabled','onchange'=>'console.log($(this).children(":selected").length)')) }}
                            </p>

                        </legend>

                    </div>

                    <div class="clrscr"></div>

                    <div class="loginbox">
                    </div>
                </form>
            </div>
        </div>

        <div id="tab-body-2" class="tab-body">
            <div id="p">
                <div class="clrscr"></div>
                <div class="crsl-items_p" data-navigation="navbtns">
                    <div class="crsl-wrap">
                                <?php
                                $curdate = date('Y-m-d H:i:s');
                                $myhistory = contestparticipantModel::select('contest.contest_name', 'contestparticipant.contest_id', 'contestparticipant.uploadfile', 'contestparticipant.uploadtopic', 'contest.contesttype', 'contestparticipant.dropbox_path')->where('contestparticipant.user_id', $profileid)->LeftJoin('contest', 'contest.ID', '=', 'contestparticipant.contest_id')->where('contest.votingenddate', '<', $curdate)->LeftJoin('user', 'user.ID', '=', 'contest.createdby')->get();


                                for ($i = 0; $i < count($myhistory); $i++) {
                                    $leaderboard = leaderboardModel::select('position')->where('contest_id', $myhistory[$i]['contest_id'])->where('user_id', $profileid)->get();
                                    ?>
                            <div class="crsl-item">
                                <div class="thumbnail">


                            <?php if ($myhistory[$i]['contesttype'] == 'p') { ?><img src="<?php echo url() . '/public/assets/upload/contest_participant_photo/' . $myhistory[$i]['uploadfile']; ?>" alt="danny antonucci"> <?php } elseif ($myhistory[$i]['contesttype'] == 'v') {
                                ?>
                                        <video width="125" height="125"  controls>											
                                            <source src="<?php echo url() . '/public/assets/upload/contest_participant_photo/' . $myhistory[$i]['uploadfile']; ?>" type="video/mp4">								
                                        </video>

    <?php
    } else {

        $topiccontest = substr(($myhistory[$i]['uploadtopic']), 0, 100) . ".....";

        echo nl2br($topiccontest);
    }
    ?>
    <?php if (count($leaderboard)) { ?><span class="postdate">Won  {{ $leaderboard[0]['position']}}<sup><?php if ($leaderboard[0]['position'] == 1) echo "st";
        else if ($leaderboard[0]['position'] == 2) echo "nd";
        else if ($leaderboard[0]['position'] == 3) echo "rd";
        else echo "th"; ?> </sup> Place</span> <?php } ?></a>
                                </div>
                                <h3><a href="<?php echo url(); ?>/contest_info/<?php echo $myhistory[$i]['contest_id']; ?>" style="color:#0896D6">{{ $myhistory[$i]['contest_name']}}</a></h3>
                            </div>
    <?php
}

if (count($myhistory) == 0) {
    echo "<h1>No Data available</h1>";
}
?> 
                    </div><!-- @end .crsl-wrap -->
                </div><!-- @end .crsl-items -->

                <nav class="slidernav">
                    <div id="navbtns" class="clearfix">
                            <!--<a href="#"><span id="txt_load_more">Load More...</span></a>-->
                    </div>
                </nav> 
            </div>
        </div>
        <!---- Following----->
        <div id="tab-body-3" class="tab-body">
            <div id="p">
                @if(isset($Message))
                <p class="alert" style="color:green; font-size:13px;">{{ $Message }}</p>
                @endif

                <table class="display" cellspacing="0" width="100%" id="dd_profile_following">
                    <thead>
                        <tr>
                            <th><span class="txt_sno">S.No</span></th>
                            <th><span class="txt_img">Image</span></th>
                            <th><span class="txt_follower_username">Follower User Name</span></th>
<?php if (Auth::user()->ID == 1) { ?>  <th>Unfollow</th> <?php } ?>
                            <th class="tr_wid_button1" align="center"><span class="txt_view">View</span></th>
                        </tr>
                    </thead>
                    <tbody>
<?php
$following = followModel::where('followerid', $profileid)
        ->select('user.profilepicture', 'user.firstname', 'user.username', 'user.lastname', 'user.ID as followinguserid')
        ->leftJoin('user', 'user.ID', '=', 'followers.userid')->where('user.status', 1)
        ->get();
for ($i = 0; $i < count($following); $i++) {
    ?>
                            <tr>
                                <td>{{ $i+1; }}</td>
                                <td align="center"><img src="{{ ($following[$i]['profilepicture']!='')?(URL::to('public/assets/upload/profile/'.$following[$i]['profilepicture'])):(URL::to('assets/inner/images/avator.png')) }}" width="50" height="50"></td>
                                <td><?php if ($following[$i]['firstname'] != '') {
        echo $following[$i]['firstname'] . ' ' . $following[$i]['lastname'];
    } else {
        echo $following[$i]['username'];
    } ?></td>
                            <?php if (Auth::user()->ID == 1) { ?>  <td><a href="<?php echo url(); ?>/unfollowforweb?followerid=<?php echo $profileid; ?>&user_id=<?php echo $following[$i]['followinguserid']; ?>&followtab=Followers" class="<?php echo "follow-link"; ?>" title="Unfollow" ></a></td> <?php } ?>


                                <td align="center"><a href="<?php echo url(); ?>/other_profile/<?php echo $following[$i]['followinguserid']; ?>" class="view-link"></a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-body-4" class="tab-body">
            <div id="p">
                @if(isset($Message))
                <p class="alert" style="color:green; font-size:13px;">{{ $Message }}</p>
                @endif

                <table class="display" cellspacing="0" width="100%" id="dd_profile_followers">
                    <thead>
                        <tr>
                            <th><Span class="txt_sno">S.No</span></th>
                            <th><span class="txt_img">Image</span></th>
                            <th><span class="txt_following_username">Following User Name</span></th>

<?php if ($profileid == Auth::user()->ID || Auth::user()->ID == 1) { ?><th><span class="txt_unfollow">Unfollow</span></th><?php } ?>
                            <th class="tr_wid_button1" align="center"><span class="txt_view">View</span></th>
                        </tr>
                    </thead>
                    <tbody>
<?php
$followers = followModel::where('followers.userid', $profileid)
        ->select('user.profilepicture', 'user.firstname', 'user.lastname', 'user.username', 'user.ID as followerid')
        ->leftJoin('user', 'user.ID', '=', 'followers.followerid')->where('user.status', 1)
        ->get();


for ($i = 0; $i < count($followers); $i++) {
    ?>
                            <tr>
                                <td>{{ $i+1; }}</td>
                                <td align="center"><img src="{{ ($followers[$i]['profilepicture']!='')?(URL::to('public/assets/upload/profile/'.$followers[$i]['profilepicture'])):(URL::to('assets/inner/images/avator.png')) }}" width="50" height="50"></td>
                                <td><?php if ($followers[$i]['firstname'] != "") {
        echo $followers[$i]['firstname'] . ' ' . $followers[$i]['lastname'];
    } else {
        echo $followers[$i]['username'];
    } ?></td>

                            <?php if ($profileid == Auth::user()->ID || Auth::user()->ID == 1) { ?>

                                    <td align="center"><a href="<?php echo url(); ?>/unfollowforweb?followerid=<?php echo $followers[$i]['followerid']; ?>&user_id=<?php echo $profileid; ?>&followtab=following" class="<?php echo "follow-link"; ?>" title="Unfollow" ></a></td>

                            <?php } ?>

                                <td align="center"><a href="<?php echo url(); ?>/other_profile/<?php echo $followers[$i]['followerid']; ?>" class="view-link"></a></td>
                            </tr>      
<?php } ?>      

                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-body-5" class="tab-body">
            <div id="p">
                <table class="display" cellspacing="0" width="100%" id="dd_profile_group">
                    <thead>
                        <tr>
                            <th><Span class="txt_sno">S.No</span></th>
                            <th><Span class="txt_img">Image</span></th>
                            <th><Span class="txt_groupname">Group Name</span></th>
                            <th><Span class="txt_memberowner">Member/Owner</span></th>

                            <th class="tr_wid_button1" align="center"><Span class="txt_view">View</span></th>
                        </tr>
                    </thead>
                    <tbody>
<?php
for ($i = 0; $i < count($memberlist); $i++) {

    $checkverifyuser = groupmemberModel::where('user_id', Auth::user()->ID)->where('group_id', $memberlist[$i])->get()->count();

    $grouplist = groupModel::select('groupname', 'grouptype', 'createdby', 'user.firstname as owner', 'groupimage', 'group.ID as groupid', 'user.ID as userid')->LeftJoin('user', 'user.ID', '=', 'group.createdby')->where('user.status', 1)->where('group.status', 1)->where('group.ID', $memberlist[$i])->get();
    if (count($grouplist)) {
        ?><tr>
                                    <td>{{  $i+1 }} </td>
                                    <td align="center"><img src="{{ ($grouplist[0]['groupimage']!='')?(URL::to('public/assets/upload/group/'.$grouplist[0]['groupimage'])):(URL::to('assets/inner/img/default_groupimage.png')) }}" width="50" height="50"></td>
                                    <td>{{ $grouplist[0]['groupname'] }}</td>
                                    <td><?php if ($grouplist[0]['createdby'] == $profileid) echo "Owner";
        else echo "Member"; ?></td>
                                    <td align="center"><a href="<?php echo url(); ?>/viewgroupmember/<?php echo $grouplist[0]['groupid']; ?>" class="view-link"></a></td>
                                </tr>
    <?php }
} ?>

                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>
@stop