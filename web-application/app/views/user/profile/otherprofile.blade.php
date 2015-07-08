@extends('header.header')
<!-- It will show the other user profile page -->
<?php
$assets_path = "assets/inner/";
?>
@section('includes')

<link type="text/css" rel="stylesheet" href="{{ URL::to('assets/inner/js/datatbl/demo_table.css') }}">
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.dataTables.js') }}"></script>
<link rel="stylesheet" media="all" type="text/css" href="{{ URL::to('assets/inner/css/jquery-ui.css') }}" />
<link rel="stylesheet" media="all" type="text/css" href="{{ URL::to('assets/inner/css/jquery-ui-timepicker-addon.css') }}" /> 

<!-- tab-menu -->
	<link rel="stylesheet" href="{{ URL::to('assets/inner/css/easy-responsive-tabs.css') }}">
 <!-- <link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css"> -->
        <style>
.demo{margin:0px auto;width:100%;}
.demo h1{margin:0 0 25px;}
.demo h3{margin:10px 0;}
pre{background-color:#FFF;}
@media only screen and (max-width:780px){
.demo{margin:5%;width:90%;}
.how-use{display:none;float:left;width:300px;}
}
#tabInfo{display:none;}
</style>

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
    window.location = "<?php echo url(); ?>/edit_profile/"+profile_id;
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

        <?php
        if ($profileid == Auth::user()->ID) {
            $authuserid = Auth::user()->ID;
			}
            ?>
			
<!--menu ends-->

<div class="tabs-wrapper">
<form id="editprofile" action="" method="post" class="form_mid">
<div class="mypro">
<div class="demo">
<div id="tabInfo">
Selected tab: <span class="tabName"></span>
</div>

<div id="verticalTab">
<div class="title-pro"><span id="txt_submenu_profile">Profile Info</span>
<span class="edt_but back_but"><a href="#" onclick="goback()" >Back</a></span>
 <?php
        if ($profileid == Auth::user()->ID) {
            $authuserid = Auth::user()->ID;
            ?>
            <span class="edt_but" ><a href="<?php echo url() . '/edit_profile/' . $authuserid; ?>" id="txt_submenu_editprofile" >Edit</a></span> 

    <?php
}
?>




</div>
<ul class="resp-tabs-list">
<li><span id="txt_personalinfo">Personal Info</span></li>
<li><span id="txt_socialinfo">Social Info</span></li>
<li><span id="txt_favorite">Favorite</span></li>
<li><span id="id="txt_timezone"">Time Zone</span></li>
</ul>

<div class="resp-tabs-container">
<div><!-- Tab1--> 

								<?php
							$followercnt = followModel::where('userid', Auth::user()->ID)->where('followerid', $profileid)->get()->count();
							

	if (Session::has('Massage'))
		$inv_suc_message = Session::get('Massage');
	?><div id="inv_success" style="color:green;text-align:center;font-size:14px"><?php if (Session::has('Massage')) echo $inv_suc_message; ?> </div>
	@if(isset($Message))
                <p class="alert_otherprofile" style="color:green;text-align:center;font-size:14px">{{ $Message }}</p>
                @endif
	
		                    <div class="loginform loginbox mar1 prof">
<?php
$profiledetail = ProfileModel::where('ID', $profileid)->get();
$timezoneList = timezoneDBModel::lists('timezonename', 'timezonevalue');
?>
                        <legend class="radius"><div class="leg_head"><span id="txt_personalinfo">Personal Info</span></div>

                            <div class="personal_info">
							
							<div class="fow_but">
							<?php if(Auth::user()->ID!=$profileid){  if ($followercnt) { ?><img src="{{ URL::to('/assets/inner/img/bell_symbol.png') }}" title="Following" ><?php } else { ?><a id="follow" class="follow_btn" href="<?php echo url(); ?>/putfollowinotherprofile?followerid=<?php echo $profileid; ?>" >Follow</a></br><?php } } ?>
							
							</div>	
							

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
									<?php if (Auth::user()->ID != $profileid) {
								?>


						

	<?php
	} ?>
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
                                <label class="rad_gen"><span id="txt_gender">Gender</span></label>
                                <input type="radio" id="gr1" name="gr" value="m" disabled <?php echo ($profiledetail[0]['gender'] == 'm' || $profiledetail[0]['gender'] == '') ? "checked" : ""; ?> >
                                <label for="gr1" id="txt_male" class="rad_gen">Male</label>
                                <input type="radio" id="gr2" name="gr"value="f" disabled <?php echo ($profiledetail[0]['gender'] == 'f') ? "checked" : ""; ?>>
                                <label for="gr2" id="txt_female" class="rad_gen">Female</label>
                            </div>

                            <p>

<?php
$originalDate = $profiledetail[0]['dateofbirth'];
$userdate = Auth::User()->dateformat;
if($userdate=='mm/dd/yy') $userdate='m/d/Y'; else  $userdate='d/m/Y'; 
$newDate = date($userdate, strtotime($originalDate));
?>
                            <div class="inp_pfix aft_rdo_mar1 m-mtop"><img src="{{ URL::to('/assets/inner/img/date_icons.png') }}" width="25" height="25"></div>
                            <input type="text" id="datepicker" disabled name="dob" placeholder="Date of Birth" title="Date of Birth" value="<?php if($profiledetail[0]['dateofbirth']!='0000-00-00'){ echo $newDate; } ?>" class="radius pfix_mar" />
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
                                <label class="rad_gen"><strong><span id="txt_maritalstatus" style="width:auot;">Marital</span></strong></label>
                                
                                <input type="radio" id="ms1" name="ms" disabled value="0" <?php echo ($profiledetail[0]['maritalstatus'] == 0) ? "checked" : ""; ?> >
                                <label for="ms1" id="txt_single" class="rad_gen" style="width:auto;">Single</label>
                                <input type="radio" id="ms2" name="ms"value="1" disabled <?php echo ($profiledetail[0]['maritalstatus'] == 1) ? "checked" : ""; ?>>
                                <label for="ms2" id="txt_married" class="rad_gen" style="width:auto;">Married</label>
                            </div>

                            <div class="clearfix"></div>

                            <p>
                            <div class="inp_pfix aft_rdo_mar1 m-mtop"><img src="{{ URL::to('/assets/inner/img/kids_icons.png') }}" width="25" height="25"></div>
                            <input type="text" id="noofkids" disabled name="noofkids" placeholder="No of Kids" title="No of Kids" value="{{ $profiledetail[0]['noofkids']  }}" class="radius pfix_mar" />
                            </p>

                        </legend>

                    </div>
	
</div><!-- Tab1 end -->
<div><!-- Tab2 -->
                    <div class="loginform loginbox mar1 prof">


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
						</div>
</div> <!-- Tab2 end -->
<div><!-- Tab3 -->
	<div class="loginform loginbox mar1 prof">
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
                            {{ Form::select('interest[]', $interestList,$userInterest, array('class'=>'SlectBox testsel radius mysel','multiple'=>'multiple','placeholder'=>'Select Interest','title'=>'Select Interest','disabled' => 'disabled','onchange'=>'console.log($(this).children(":selected").length)','style'=>'width:97%')) }}

                            </p>

                        </legend>

	</div>

</div><!-- Tab3 end -->
<div><!-- Tab4 -->
	<div class="loginform loginbox mar1 prof">
	<legend class="radius"><div class="leg_head" id="txt_timezone"  >Time Zone</div>

                            <p>
                            <div class="inp_pfix mb_sel_mt"><img src="{{ URL::to('/assets/inner/img/timezone_icons.png') }}" width="25" height="25"></div>
                            {{ Form::select('timezone', array("Select Time Zone")+$timezoneList,isset($old_data['timezone'])?$old_data['timezone']:$profiledetail[0]['timezone'], array('class'=>'SlectBox testsel radius','placeholder'=>'Select Timezone','title'=>'Select Timezone', 'disabled' => 'disabled','onchange'=>'console.log($(this).children(":selected").length)')) }}
                            </p>
							
							<p>
							<div class="inp_pfix mb_sel_mt"><img src="{{ URL::to('/assets/inner/img/timezone_icons.png') }}" width="25" height="25"></div>
							<?php 
							$dateformat = array("mm/dd/yy"=>"mm/dd/yy", "dd/mm/yy"=>"dd/mm/yy");
							//print_r $old_data['dateformat'];
							?>
							{{ Form::select('dateformat',$dateformat,isset($old_data['dateformat'])?$old_data['dateformat']:$profiledetail[0]['dateformat'], array('class'=>'SlectBox testsel radius mysel','placeholder'=>'Select Date format','title'=>'Select Dateformat')) }}
							</p>

                        </legend>
	</div>
</div><!-- Tab4 end -->
</div>
<br />
<div style="height: 30px; clear: both"></div>

 </div></div>
 <div class="clrscr"></div>
 </div>
 	<!-- <Section 2 > -->
		<div class="myhis">
		
		<div class="title-pro"><span id="txt_submenu_history">History</span></div>
			            <div id="p" class="tbrgt my_his_blk">
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
		<!-- Section 3 -->
		<div class="myhis_follow ">
		<div class="title-pro"><span id="txt_followers">Followers</span></div>
		           <div id="p" class="tbrgt myhis_tabl">
                

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
		<!-- Section 4-->
		<div class="myhis_follow ">
		<div class="title-pro"><span id="txt_following">Following</span></div>
			           <div id="p" class="tbrgt myhis_tabl">
                

                <table class="display " cellspacing="0" width="100%" id="dd_profile_followers">
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
		<!-- Section 5-->
		<div class="myhis_follow ">
		
		<div class="title-pro"><span class="mnu_group">Group</span></div>
		           <div id="p" class="tbrgt myhis_tabl">
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
@stop