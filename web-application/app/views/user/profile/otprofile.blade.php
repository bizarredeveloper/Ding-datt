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
<!--menu ends-->
<!--menu ends-->
	<div class="tabs-wrapper1">
      
		<!-- <div style="width:20%; float:left; display:block;">  -->
			<input type="radio" name="tab" id="tab1" class="tab-head" checked="checked"/>
			<label for="tab1" class="tableft">Profile Info</label>
			<input type="radio" name="tab" id="tab2" class="tab-head" />
			<label for="tab2" class="tableft">History</label>
			<input type="radio" name="tab" id="tab3" class="tab-head" />
			<label for="tab3" class="tableft">Following</label>
			<input type="radio" name="tab" id="tab4" class="tab-head" />
			<label for="tab4" class="tableft">Followers</label>
			<input type="radio" name="tab" id="tab5" class="tab-head" />
			<label for="tab5" class="tableft">Group</label>
         <!-- </div>  -->
		
        <div class="mbblk">
        	<select class="radius sel_lang">
            	<option>My Profile</option>
                <option>History</option>
                <option>Following</option>
                <option>Followers</option>
                <option>Group</option>
            </select>
        </div>
	  
	  
        <div class="tab-body-wrapper" style="min-height:850px; position: relative; background:#d5d5d5; top: -200px;"><!--  -->
		
		
        
        	<div id="tab-body-1" class="tab-body">
				<div id="p" class="tbrgt">
    
    			<div class="clrscr"></div>
        			<form id="editprofile" action="" method="post" class="form_mid">
    	<div class="loginform loginbox mar1">
       		 <legend class="radius"><div class="leg_head">Personal Info</div>
             
             	<div class="personal_info">
                	<div><img src="img/thumb02.jpg" width="100" height="100" class="roundedimg brd_grn"></div>
                    <div class="per_det">
                    	<div><strong>First Name:</strong> Amirtharaj</div>
                    	<div><strong>Last Name:</strong> S</div>
                        <div><strong>User Name:</strong> DD025_Amirtharaj</div>
                    </div>
                </div>
                
            	<p>
           	  <div class="inp_pfix aft_up_mar"><img src="img/phone_icons.png" width="25" height="25"></div>
              <input type="text" id="phone" name="phone" placeholder="Phone" title="Phone" value="9876543210" class="radius pfix_mar" />
                </p>
                
            	<p>
           	  <div class="inp_pfix"><img src="img/email_icons.png" width="25" height="25"></div>
              <input type="text" id="email" name="email" placeholder="Email" title="Email" value="amirtharaj@bizarresoftware.in" class="radius pfix_mar" />
                </p>
                                
            <div class="rdogrp">
              	<label><strong>Gender</strong></label>
                <div class="mb_brk"></div>
                <input type="radio" id="gr1" name="gr" value="" checked>
                <label for="gr1">Male</label>
                <input type="radio" id="gr2" name="gr"value="">
                <label for="gr2">Female</label>
			</div>
                
                <p><!--rdomt59-->
           	  <div class="inp_pfix aft_rdo_mar"><img src="img/date_icons.png" width="25" height="25"></div>
              <input type="text" id="datepicker" name="dob" placeholder="Date of Birth" title="Date of Birth" value="" class="radius pfix_mar" />
                </p>
                
                <p>
           	  <div class="inp_pfix "><img src="img/location_icons.png" width="25" height="25"></div>
              <input type="text" id="hometown" name="hometown" placeholder="Home Town" title="Home Town" value="Coimbatore" class="radius pfix_mar" />
                </p>
                
                <p>
           	  <div class="inp_pfix"><img src="img/school_icons.png" width="25" height="25"></div>
              <input type="text" id="school" name="school" placeholder="School" title="School" value="NVSHS" class="radius pfix_mar" />
                </p>
                
                <p>
           	  <div class="inp_pfix"><img src="img/occupations_icons.png" width="25" height="25"></div>
              <input type="text" id="occupation" name="occupation" placeholder="Occupation / Profession" title="Occupation" value="Web Designer" class="radius pfix_mar" />
                </p>

            
            <div class="clearfix"></div>
                
            <div class="rdogrp">
              	<label><strong>Marital Status</strong></label>
                <div class="mb_brk"></div>
                <input type="radio" id="ms1" name="ms" value="" >
                <label for="ms1">Single</label>
                <input type="radio" id="ms2" name="ms"value="" checked>
                <label for="ms2">Married</label>
			</div>
            
            <div class="clearfix"></div>
                
                <p><!--rdomt59-->
           	  <div class="inp_pfix aft_rdo_mar"><img src="img/kids_icons.png" width="25" height="25"></div>
              <input type="text" id="noofkids" name="noofkids" placeholder="No of Kids" title="No of Kids" value="1" class="radius pfix_mar" />
                </p>
                
                </legend>
            
           </div>
           
           <div class="loginform loginbox mar2">
           
              <legend class="radius"><div class="leg_head">Social Info</div>
                <p>
           	  <div class="inp_pfix"><img src="img/facebook_icons.png" width="25" height="25"></div>
              <input type="text" id="facebookpage" name="facebookpage" placeholder="Facebook Page" title="Facebook Page" value="http://facebook.com/amirtharaj" class="radius pfix_mar" />
                </p>
                
                <p>
           	  <div class="inp_pfix"><img src="img/twitter_icons.png" width="25" height="25"></div>
              <input type="text" id="twitterpage" name="twitterpage" placeholder="Twitter Page" title="Twitter Page" value="http://twitter.com/amirtharaj" class="radius pfix_mar" />
                </p>
                
                <p>
           	  <div class="inp_pfix"><img src="img/instagram_icons.png" width="25" height="25"></div>
              <input type="text" id="instagrampage" name="instagrampage" placeholder="Instagram Page" title="Instagram Page" value="http://instagram.com/amirtharaj" class="radius pfix_mar" />
                </p>
                </legend>
                
            <legend class="radius"><div class="leg_head">Favorite</div>
               
                <p>
           	  <div class="inp_pfix"><img src="img/holiday_icons.png" width="25" height="25"></div>
              <input type="text" id="favholidayspot" name="favholidayspot" placeholder="Favorite Holiday Spot" title="Favorite Holiday Spot" value="India, UK" class="radius pfix_mar" />
                </p>
                
                <p>
           	  <div class="inp_pfix mb_sel_mt"><img src="img/interest_icons.png" width="25" height="25"></div>
              <select multiple="multiple" placeholder="Select Interest" title="Interest" onchange="console.log($(this).children(':selected').length)" class="testsel radius">
        <option selected value="">Select Interest</option>
        <option value="Cinema">Cinema</option>
        <option value="Music">Music</option>
        <option value="Sports">Sports</option>
        <option value="Photography">Photography</option>
        <option value="Cook">Cook</option>
        <option value="Eat">Eat</option>
   </select>
                </p>
                
                </legend>
                
                <legend class="radius"><div class="leg_head">Time Zone</div>
               
                <p>
           	  <div class="inp_pfix mb_sel_mt"><img src="img/timezone_icons.png" width="25" height="25"></div>
              <select multiple="multiple" placeholder="Select Interest" title="Interest" onchange="console.log($(this).children(':selected').length)" class="testsel radius">
        <option selected value="">Select Timezone</option>
        <option value="">Eniwetok, Kwajalein</option>
        <option value="">Midway Island, Samoa</option>
        <option value="">Hawaii</option>
        <option value="">Alaska</option>
        <option value="">Pacific Time (US & Canada)</option>
        <option value="">Mountain Time (US & Canada)</option>
        <option value="">Central Time (US & Canada), Mexico City</option>
        <option value="">Eastern Time (US & Canada), Bogota, Lima</option>
        <option value="">Atlantic Time (Canada), Caracas, La Paz</option>
        <option value="">Newfoundland</option>
        <option value="">Brazil, Buenos Aires, Georgetown</option>
        <option value="">Mid-Atlantic</option>
        <option value="">Azores, Cape Verde Islands</option>
        <option value="">Western Europe Time, London, Lisbon, Casablanca</option>
        <option value="">Brussels, Copenhagen, Madrid, Paris</option>
        <option value="">Kaliningrad, South Africa</option>
        <option value="">Baghdad, Riyadh, Moscow, St. Petersburg</option>
        <option value="">Tehran</option>
        <option value="">Abu Dhabi, Muscat, Baku, Tbilisi</option>
        <option value="">Kabul</option>
        <option value="">Ekaterinburg, Islamabad, Karachi, Tashkent</option>
        <option value="">Bombay, Calcutta, Madras, New Delhi</option>
        <option value="">Kathmandu</option>
        <option value="">Almaty, Dhaka, Colombo</option>
        <option value="">Bangkok, Hanoi, Jakarta</option>
        <option value="">Beijing, Perth, Singapore, Hong Kong</option>
        <option value="">Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
        <option value="">Adelaide, Darwin</option>
        <option value="">Eastern Australia, Guam, Vladivostok</option>
        <option value="">Magadan, Solomon Islands, New Caledonia</option>
        <option value="">Auckland, Wellington, Fiji, Kamchatka</option>	
   </select>
                </p>
                
                </legend>
                          
           </div>
           
           <div class="clrscr"></div>
           
            <div class="loginbox">
                <p><center>
                	<button class="radius martop_10" name="client_login">Update Profile</button>
                </center></p> 
            </div>
           </form>
				</div>
            </div>
            
			<div id="tab-body-2" class="tab-body">
				<div id="p" class="tbrgt">
    
    	<div class="clrscr"></div>

    <div class="crsl-items_p" data-navigation="navbtns">
      <div class="crsl-wrap">
        <div class="crsl-item">
          <div class="thumbnail">
            <a href="contest_info.html"><img src="img/thumb01.jpg" alt="nyc subway">
            <span class="postdate">Won 1<sup>st</sup> Place</span></a>
          </div>
          
          <h3><a href="contest_info.html">Lorem Ipsum Dolor Sit</a></h3>

        </div><!-- post #1 -->
        
        <div class="crsl-item">
          <div class="thumbnail">
            <a href="contest_info.html"><img src="img/thumb02.jpg" alt="danny antonucci">
            <span class="postdate">Won 2<sup>nd</sup> Place</span></a>
          </div>
          
          <h3><a href="contest_info.html">A Look Back over A.K.A Cartoon</a></h3>
          
        </div><!-- post #2 -->
        
        <div class="crsl-item">
          <div class="thumbnail">
            <a href="contest_info.html"><img src="img/thumb03.jpg" alt="watercolor paints">
            <span class="postdate">Won 2<sup>nd</sup> Place</span></a>
          </div>
          
          <h3><a href="contest_info.html">Watercoloring for Beginners</a></h3>
          
        </div><!-- post #3 -->
        
        <div class="crsl-item">
          <div class="thumbnail">
            <a href="contest_info.html"><img src="img/thumb04.jpg" alt="apple ipod classic photo">
            <span class="postdate">Won 3<sup>rd</sup> Place</span></a>
          </div>
          
          <h3><a href="contest_info.html">Classic iPods are Back!</a></h3>
          
        </div><!-- post #4 -->
        
        <div class="crsl-item">
          <div class="thumbnail">
            <a href="contest_info.html"><img src="img/thumb01.jpg" alt="nyc subway">
            <span class="postdate">Won 5<sup>th</sup> Place</span></a>
          </div>
          
          <h3><a href="contest_info.html">Lorem Ipsum Dolor Sit</a></h3>

        </div><!-- post #1 -->
        
        <div class="crsl-item">
          <div class="thumbnail">
            <a href="contest_info.html"><img src="img/thumb04.jpg" alt="apple ipod classic photo">
            <span class="postdate">Won 20<sup>th</sup> Place</span></a>
          </div>
          
          <h3><a href="contest_info.html">Classic iPods are Back!</a></h3>

        </div><!-- post #4 -->
        
        <div class="crsl-item">
          <div class="thumbnail">
            <a href="contest_info.html"><img src="img/thumb05.jpg" alt="web design magazines">
            <span class="postdate">Won 20<sup>th</sup> Place</span></a>
          </div>
          
          <h3><a href="contest_info.html">The 10 Best Web Design Magazines</a></h3>

        </div><!-- post #5 -->
        
        <div class="crsl-item">
          <div class="thumbnail">
            <a href="contest_info.html"><img src="img/thumb04.jpg" alt="apple ipod classic photo">
            <span class="postdate">Won 20<sup>th</sup> Place</span></a>
          </div>
          
          <h3><a href="contest_info.html">Classic iPods are Back!</a></h3>

        </div><!-- post #4 -->
        
        
        <div class="crsl-item">
          <div class="thumbnail">
            <a href="contest_info.html"><img src="img/thumb05.jpg" alt="web design magazines">
            <span class="postdate">Won 5<sup>th</sup> Place</span></a>
          </div>
          
          <h3><a href="#">The 10 Best Web Design Magazines</a></h3>

        </div><!-- post #5 -->
      </div><!-- @end .crsl-wrap -->
    </div><!-- @end .crsl-items -->
   
     <nav class="slidernav">
      <div id="navbtns" class="clearfix">
        <a href="#">Load More...</a>
      </div>
    </nav> 
    </div>
    </div>
            
            <div id="tab-body-3" class="tab-body">
				<div id="p" class="tbrgt">
    
    	<table class="display" cellspacing="0" width="100%" id="dd_profile_following">
    <thead>
        <tr>
            <th>Image</th>
            <th>Following User Name</th>
            <th>Status</th>
            <th class="tr_wid_button1" align="center">View</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="#" class="follow-link"></a></td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
       <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="#" class="unfollow-link"></a></td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="#" class="follow-link"></a></td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="#" class="follow-link"></a></td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="#" class="follow-link"></a></td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="#" class="follow-link"></a></td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="#" class="follow-link"></a></td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="#" class="follow-link"></a></td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="#" class="follow-link"></a></td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="#" class="follow-link"></a></td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="#" class="follow-link"></a></td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
       
        
    </tbody>
    </table>
    </div>
    </div>
    
   			<div id="tab-body-4" class="tab-body">
				<div id="p" class="tbrgt">
    
    	<table class="display" cellspacing="0" width="100%" id="dd_profile_followers">
    <thead>
        <tr>
            <th>Image</th>
            <th>Follower User Name</th>
            <th class="tr_wid_button1" align="center">View</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
       <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Linda Paul</td>
            <td align="center"><a href="other_profile.html" class="view-link"></a></td>
        </tr>
       
        
    </tbody>
    </table>
    </div>
    </div>
    
    		<div id="tab-body-5" class="tab-body">
				<div id="p" class="tbrgt">
    <table class="display" cellspacing="0" width="100%" id="dd_profile_group">
    <thead>
        <tr style="background:#0896D6;">
            <th>Image</th>
            <th>Group Name</th>
            <th>Status</th>
            <th class="tr_wid_button1" align="center">View</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>5 star Group</td>
            <td align="center"><a href="#" class="group-link"></a></td>
            <td align="center"><a href="group_member.html" class="view-link"></a></td>
        </tr>
       <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>BIZARRE</td>
            <td align="center"><a href="#" class="group-link"></a></td>
            <td align="center"><a href="group_member.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Super kings</td>
            <td align="center"><a href="#" class="ungroup-link"></a></td>
            <td align="center"><a href="group_member.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>Monsters</td>
            <td align="center"><a href="#" class="group-link"></a></td>
            <td align="center"><a href="group_member.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>5 star Group</td>
            <td align="center"><a href="#" class="group-link"></a></td>
            <td align="center"><a href="group_member.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>5 star Group</td>
            <td align="center"><a href="#" class="group-link"></a></td>
            <td align="center"><a href="group_member.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>5 star Group</td>
            <td align="center"><a href="#" class="group-link"></a></td>
            <td align="center"><a href="group_member.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>5 star Group</td>
            <td align="center"><a href="#" class="group-link"></a></td>
            <td align="center"><a href="group_member.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>5 star Group</td>
            <td align="center"><a href="#" class="group-link"></a></td>
            <td align="center"><a href="group_member.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>5 star Group</td>
            <td align="center"><a href="#" class="group-link"></a></td>
            <td align="center"><a href="group_member.html" class="view-link"></a></td>
        </tr>
        <tr>
            <td align="center"><img src="images/thumbs/3.jpg" width="50" height="50"></td>
            <td>5 star Group</td>
            <td align="center"><a href="#" class="group-link"></a></td>
            <td align="center"><a href="group_member.html" class="view-link"></a></td>
        </tr>
       
        
    </tbody>
    </table>
    	
    </div>
    </div>
    
    	</div>
    </div>
    
    <div class="clrscr"></div>
    
    <div class="ddwidth">
    <div class="fscn_footer fleft">
        <ul>
            <li><a href="#">About</a></li>
            <li><a href="#">Support</a></li>
            <li><a href="#">Terms</a></li>
            <li><a href="#">Privacy</a></li>
        </ul>
    </div>
    <div class="dev_footer fright">
        Developed by <a href="http://bizarresoftware.in">BIZARRE Software Solutions</a>
    </div>
</div>

</body>
</html>