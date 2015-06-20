<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta property="fb:app_id" content="751664208282208"/>
  <meta property="og:image" content="{{ URL::to('public/assets/inner/img/logo_face_share.png')}}"/>
  <title>DingDatt</title>
  <link rel="shortcut icon" href="{{ URL::to('assets/inner/img/dingdatt_favicon.ico') }}" type="image/x-icon">
  <link rel="stylesheet" type="text/css" media="all" href="{{ URL::to('assets/inner/css/styles.css') }}">
  <script type="text/javascript" src="{{ URL::to('assets/inner/js/jquery-1.9.1.min.js') }}"></script>
	<script type="text/javascript">var switchTo5x=true;</script>
	
	<!--<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
	<script type="text/javascript">stLight.options({publisher: "d4ab7898-2b6c-4cc4-a753-12192e1bb354", shorten:false, doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>-->
	
	@yield('includes')
	<script type="text/javascript" >

	$(document).ready(function() { 
			if($('#sessionlanguage').val()=='')
			changelanguage('value_en');
			else
			changelanguage($('#sessionlanguage').val());	
	$( "#languageid" ).change(function(e) {
			var languagename = $(this).val();
					e.preventDefault();
					changelanguage('value_'+languagename);		  
	});   
	});
	function changelanguage(languagename)
	{	 
		var page_name = $('#pagename').val();   
		if(page_name=="edit_contest"||page_name=="contest_info"||page_name=="groupmember" || page_name=="other_profile" || page_name=="userlist"||page_name=='editgroup')
		var lanurl="../changeLanguage";
		else
		var lanurl="changeLanguage";
		var dataString = 'languagename='+languagename+'&page_name='+page_name;   
		$.ajax({
			type: "POST",
			url : lanurl,
			data : dataString,
			success : function(data){   
				for(var i=0; i<data.length;i++)
				{ 
					console.log(data[i][languagename]);
					$('#sessionlanguage').val(languagename);
					if(data[i]['ctrlCaptionId']=='txt_submit')
					{
						$('#'+data[i]['ctrlCaptionId']).val(data[i][languagename]);
						$('.'+data[i]['ctrlCaptionId']).val(data[i][languagename]);
					}
					else
					{
						$('#'+data[i]['ctrlCaptionId']).attr("placeholder",data[i][languagename]); 
						$('.'+data[i]['ctrlCaptionId']).attr("placeholder",data[i][languagename]); 
						$('#'+data[i]['ctrlCaptionId']).html(data[i][languagename]);
						$('.'+data[i]['ctrlCaptionId']).html(data[i][languagename]);
					}
				}          
			}
		}); 
	}
	</script>
	<!---- For Multi select --------->
	  	<script src="{{ URL::to('assets/inner/js/jquery.sumoselect.js') }}"></script>
    <link href="{{ URL::to('assets/inner/css/sumoselect.css') }}" rel="stylesheet" />
    <script type="text/javascript">
        $(document).ready(function () {
            window.asd = $('.SlectBox').SumoSelect({ csvDispCount: 3 });
            window.test = $('.testsel').SumoSelect({okCancelInMulti:true });
        });
		
    </script>
	
	<script type="text/javascript">
	function goback()
	{
	window.history.back();	
	}
	
	</script>
	<!----- Multi Select End --------->
</head>
<body >
<input type="hidden" id="sessionlanguage" value="<?php echo Session::get('language'); ?>">
<div class="head_con">
<div class="logo_con">
<img src="{{ URL::to('assets/inner/img/DingDatt_logo_web1.png') }}" width="150" height="51">
</div>
<?php 
 //echo groupModel::where('createdby',Auth::user()->ID)->get();
$admin = invitememberforgroupModel::where('group.createdby',Auth::user()->ID)->where('invitememberforgroup.invitetype','u')->LeftJoin('group','group.ID','=','invitememberforgroup.group_id')->get()->count();
 
 
 $user = invitememberforgroupModel::where('user_id',Auth::user()->ID)->where('invitetype','m')->get()->count();
?>
<div class="fright">

		<div class="noty fleft">
    	<a href="<?php echo url(); ?>/accepttherequest?accepttype=admin">
        	<span class="circ">{{ $admin }}</span>	<span id="txt_admingroup">Admin Group</span>
        </a>
        <a href="<?php echo url(); ?>/accepttherequest?accepttype=user">
        	<span class="circ">{{$user}}</span>	<span id="txt_adminrequest">Admin Request</span>
        </a>
	<?php
	
		$languageDetails = languagenameModel::lists('language_name','language_key');
		$lang=explode('_',Session::get('language'));
	?>
	{{ Form::select('language', array(''=>'Language')+$languageDetails,$lang[1], array('id'=> 'languageid','class'=>'radius sel_lang'))}}
</div></div>
</div>
<div class="clrscr"></div>
<?php
$ur_string=Request::segment(1);
$login_first_name=Auth::user()->firstname;
if($login_first_name!='')
$loginusername=$login_first_name." ".Auth::user()->lastname;
else
$loginusername=Auth::user()->username;
?>
<!--menu starts-->
<div class="menu-bar">
        <div class="menu-toggler">
            <div class="mb_menuicon"><img src="{{ URL::to('assets/inner/img/mb_menu.png') }}"></div>
            <div class="mb_welcome"><span id="txt_welcome" class="txt_welcome">Welcome</span> <span id="comp_name">{{ $loginusername }}</span></div>
         </div>

        <div class="top-menu">
            <ul>
                <li <?php echo ($ur_string=="webpanel"||$ur_string=="contest_info")?"class='active'":""; ?> ><a href="{{ URL::to('webpanel') }}"><span id="mnu_contestlist">Contest List</span></a></li>               
                <li <?php echo ($ur_string=="contest")?"class='active'":""; ?> ><a href="{{ URL::to('contest') }}"><span id="mnu_createcontest">Create Contest</span></a></li>
                <li <?php echo ($ur_string=="my_contest"||$ur_string=="edit_contest")?"class='active'":""; ?>><a href="{{ URL::to('my_contest') }}"><span id="mnu_mycontest">My Contest</span></a></li>
                <li <?php echo ($ur_string=="group")?"class='active'":""; ?>><a href="{{ URL::to('group') }}"><span id="mnu_group">Group</span></a></li>
                <li <?php echo ($ur_string=="other_profile" && Request::segment(2)!=Auth::user()->ID)?"class='active'":""; ?>><!--<a href="#"><span id="mnu_otherprofile">Other Profile</span></a>--></li>
                <div class="clearfix"></div>
            </ul>
        </div>
        
        <div class="top-menu login-section">
            <ul>
                <li class="desk_welcome"><span id="txt_welcome" class="txt_welcome active">Welcome</span> <span id="comp_name">{{ $loginusername }}</span></li>

					<li ><a href="{{ URL::to('other_profile/'.Auth::user()->ID) }}" ><span id="txt_viewmyprofile" <?php echo ($ur_string=="edit_profile"||($ur_string=="other_profile" && Request::segment(2)==Auth::user()->ID))?"class='active'":""; ?>>My Profile</a></li>
					
                <li><a href="{{ URL::to('logout') }}"><span id="txt_menu_logout">Logout</span></a></li>
                <div class="clearfix"></div>
            </ul>
        </div>
        
        <div class="clearfix"></div>
    </div>
<!--menu ends-->
@yield('body')
<div style="position:absolute; width:100%;">
	<div class="fscn_footer fleft">
		<ul>
			<li><a href="#"><span id="txt_menu_about">About</span></a></li>
			<li><a href="#"><span id="txt_menu_support">Support</span></a></li>
			<li><a href="#"><span id="txt_menu_terms">Terms</span></a></li>
			<li><a href="#"><span id="txt_menu_privacy">Privacy</span></a></li>
		</ul>
	</div>
	<div class="dev_footer fright">
		<span id="txt_menu_developedby">Developed by</span> <a href="http://bizarresoftware.in">BIZARRE Software Solutions</a>
	</div>
    </div>
</div>
</body>
</html>