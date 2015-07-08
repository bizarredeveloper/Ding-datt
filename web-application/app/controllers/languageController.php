<?php
class languageController extends BaseController
{
    public function getlanguageDetails()
    {
	
	$page_name = $_POST['page_name'];
	$languagename =  $_POST['languagename']; 
	if(Session::get('language')=="")
	{ 
		$languagename='value_en'; 
	}
		
	////// Login Page//////
	if($page_name=='login')
	{
		$labelname = ['txt_admingroup','txt_adminrequest','txt_login','txt_submit','txt_forgotpassword','txt_signup','txt_homepage','txt_homehead','txt_face_signin','txt_ggl_signin','txt_twit_signin','txt_pinterest_signin','txt_keeplogin','txt_loginto','alert_enterusername','alert_enterpassword','alert_invaliduserpass','alert_enteruseroremail','alert_enteruseroremail','pch_username','pch_password','pch_email','pch_useroremail','txt_menu_about','txt_menu_support','txt_menu_terms','txt_menu_privacy','txt_menu_developedby'];
	}
	elseif($page_name=='forgotpass')
	{
		$labelname = ['txt_admingroup','txt_adminrequest','txt_login','txt_submit','txt_forgotpassword','txt_signup','txt_homepage','txt_homehead','pch_useroremail','txt_resendpassword','alert_enteruseroremail','alert_sendpasssuccess','alert_useroremailnotfound','txt_menu_about','txt_menu_support','txt_menu_terms','txt_menu_privacy','txt_menu_developedby'];
	}
	elseif($page_name=='profile')
	{
		$labelname = ['txt_admingroup','txt_adminrequest','txt_signup','txt_login','txt_homepage','txt_homehead','txt_termsconditions','txt_signupto','alert_enterusername','alert_enteremail','alert_enterpassword','alert_validemail','alert_enterdob','alert_alreadyuser','alertr_emailalready','alert_minpass5','pch_username','pch_password','pch_email','pch_dob','txt_menu_about','txt_menu_support','txt_menu_terms','txt_menu_privacy','txt_menu_developedby','txt_myprofile'];
	}
	elseif($page_name=='webpanel')
	{
		$labelname = ['txt_admingroup','txt_adminrequest','mnu_contestlist','mnu_createcontest','mnu_mycontest','mnu_myhistory','mnu_group','mnu_otherprofile',
					'txt_welcome','txt_menu_logout','txt_myprofile','txt_menu_about','txt_menu_support','txt_menu_terms','txt_menu_privacy',
					'txt_menu_developedby','txt_photocontest','txt_videocontest','txt_topicocontest','txt_currentcontest','txt_upcommingcontest','txt_archivecontest',
					'txt_privatecontest','txt_contestlist','pch_searchcontest'];
	}
	elseif($page_name=='edit_profile')
	{
		$labelname = ['txt_admingroup','txt_adminrequest','mnu_contestlist','mnu_createcontest','mnu_mycontest','mnu_myhistory','mnu_group','mnu_otherprofile',
		'txt_welcome','txt_menu_logout','txt_myprofile','txt_logininfo','txt_socialinfo','txt_favorite','txt_personalinfo','txt_gender','txt_maritalstatus',
		'txt_others','txt_male','txt_female','txt_single','txt_married','txt_changeprofileimage','txt_updateprofile','txt_user_update_msg','alert_enterusername',
		'alert_alreadyuser','alert_enterpassword','alert_minpass5','alert_passconfnotmatch','alert_enteremail','alert_validemail', 'alertr_emailalready', 
		'pch_username','pch_password','pch_confirmpassword','pch_facebookpage','pch_twitterpage','pch_instagrampage','pch_favholidayspot','pch_interest',
		'pch_firstname','pch_lastname','pch_mobile','pch_email','pch_hometown','pch_school','pch_occupation','pch_noofkids','txt_menu_about','txt_menu_support',
		'txt_menu_terms','txt_menu_privacy','txt_menu_developedby'];
	}	
	elseif($page_name=='contest')
	{
		$labelname = ['txt_admingroup','txt_adminrequest','mnu_contestlist','mnu_createcontest','mnu_mycontest','mnu_myhistory','txt_welcome','txt_menu_logout','txt_myprofile','mnu_group','mnu_otherprofile',
		'txt_menu_about','txt_menu_support','txt_menu_terms','txt_menu_privacy','txt_menu_developedby','txt_createcontest','txt_contestinfo','txt_favorite',
		'txt_contestschedule','txt_votingschedule','txt_sponsorinfo','txt_contesttype','txt_photo','txt_video','txt_topic','txt_uploadcontestimage',
		'txt_uploadsponserimage','pch_contestname','pch_nofopartis0','pch_contestprize','conteststart','contestend','pch_sponsorname','votingstart',
		'votingend','alert_themephoto','pch_contestinfo'];
	}	
	elseif($page_name=='edit_contest')
	{
		$labelname = ['txt_admingroup','txt_adminrequest','mnu_contestlist','mnu_createcontest','mnu_mycontest','mnu_myhistory','txt_welcome','txt_menu_logout','txt_myprofile','txt_menu_about','mnu_group','mnu_otherprofile',
		'txt_menu_support','txt_menu_terms','txt_menu_privacy','txt_menu_developedby','txt_createcontest','txt_contestinfo','txt_favorite','txt_contestschedule',
		'txt_votingschedule','txt_sponsorinfo','txt_contesttype','txt_photo','txt_video','txt_topic','txt_uploadcontestimage','txt_uploadsponserimage',
		'pch_contestname','pch_nofopartis0','pch_contestprize','pch_contestinfo','conteststart','contestend','pch_sponsorname','votingstart','votingend',
		'txt_changecontestimage','txt_editcontest','txt_updatecontest','txt_timezone'];
	}
	elseif($page_name=='my_contest')
	{
		$labelname = ['txt_admingroup','txt_adminrequest','mnu_contestlist','mnu_createcontest','mnu_mycontest','mnu_myhistory','mnu_group','mnu_otherprofile','txt_welcome','txt_menu_logout','txt_myprofile','txt_menu_about',
		'txt_menu_support','txt_menu_terms','txt_menu_privacy','txt_menu_developedby','pch_searchcontest','txt_contestlist','txt_participatedcontest','txt_createdcontest'];
	}
	elseif($page_name=='contest_info')
	{
		$labelname = ['txt_admingroup','txt_adminrequest','mnu_contestlist','mnu_createcontest','mnu_mycontest','mnu_myhistory','mnu_group','mnu_otherprofile','txt_welcome','txt_menu_logout','txt_myprofile','txt_menu_about',
		'txt_menu_support','txt_menu_terms','txt_menu_privacy','txt_menu_developedby','pch_searchcontest','txt_contestlist','txt_participatedcontest','txt_createdcontest','txt_contestinfo','txt_join','txt_invite','txt_gallery','txt_leaderboard','txt_share','mnu_follower','pch_contestname','txt_contesttype','conteststart','contestend','votingstart','votingend','txt_prize','txt_noofparticipant','txt_contestdescription','txt_organizer','txt_submit','txt_img','txt_groupname','txt_invite','txt_view','txt_invite_selected','txt_followername','txt_img_postedby','txt_sharewith','txt_contestantname','txt_noofvotes','txt_rank'];
	}
	elseif($page_name=='group')
	{
	$labelname = ['txt_admingroup','txt_adminrequest','mnu_contestlist','mnu_createcontest','mnu_mycontest','mnu_myhistory','mnu_group','mnu_otherprofile','txt_welcome','txt_menu_logout','txt_myprofile','txt_menu_about',
		'txt_menu_support','txt_menu_terms','txt_menu_privacy','txt_menu_developedby','pch_searchcontest','txt_following','txt_followers','txt_invite','txt_uploadgroupimage','txt_open','txt_private','txt_grouptype','txt_groupinfo','alert_groupname','submnu_creategroup','submenu_grouplist','txt_groupname','txt_img','txt_sno','txt_grptype','txt_grpowner','txt_view','txt_edit','txt_delete','pch_searchgroup','txt_exit'];
	
	}
	elseif($page_name=='groupmember')
	{
	
	$labelname = ['txt_admingroup','txt_adminrequest','mnu_contestlist','mnu_createcontest','mnu_mycontest','mnu_myhistory','mnu_group','mnu_otherprofile','txt_welcome','txt_menu_logout','txt_myprofile','txt_menu_about',
		'txt_menu_support','txt_menu_terms','txt_menu_privacy','txt_menu_developedby','pch_searchcontest','txt_groupmember','txt_img','txt_sno','txt_remove','txt_view','txt_memname','txt_back','btn_addmember','txt_join'];
	}
	elseif($page_name=="other_profile")
	{
	$labelname = ['txt_admingroup','txt_adminrequest','mnu_contestlist','mnu_createcontest','mnu_mycontest','mnu_myhistory','mnu_group','mnu_otherprofile','txt_welcome','txt_menu_logout','txt_myprofile','txt_menu_about',
		'txt_menu_support','txt_menu_terms','txt_menu_privacy','txt_menu_developedby','pch_searchcontest','txt_submenu_profile','txt_submenu_history','txt_submenu_profile','txt_following','txt_followers','mnu_group','txt_submenu_editprofile','txt_myprofile','txt_logininfo','txt_socialinfo','txt_favorite','txt_personalinfo','txt_gender','txt_maritalstatus',
		'txt_others','txt_male','txt_female','txt_single','txt_married','txt_changeprofileimage','txt_updateprofile','txt_user_update_msg','alert_enterusername',
		'alert_alreadyuser','alert_enterpassword','alert_minpass5','alert_passconfnotmatch','alert_enteremail','alert_validemail', 'alertr_emailalready', 
		'pch_username','pch_password','pch_confirmpassword','pch_facebookpage','pch_twitterpage','pch_instagrampage','pch_favholidayspot','pch_interest',
		'pch_firstname','pch_lastname','pch_mobile','pch_email','pch_hometown','pch_school','pch_occupation','pch_noofkids','txt_username','txt_timezone','txt_load_more','txt_following_username','txt_status','txt_view','txt_follower_username','txt_groupname','txt_img','txt_sno','txt_back','txt_unfollow'];
	}
	elseif($page_name=="acceptmemberlist")
	{
	$labelname = ['txt_admingroup','txt_adminrequest','mnu_contestlist','mnu_createcontest','mnu_mycontest','mnu_myhistory','mnu_group','mnu_otherprofile','txt_welcome','txt_menu_logout','txt_myprofile','txt_menu_about',
		'txt_menu_support','txt_menu_terms','txt_menu_privacy','txt_menu_developedby','pch_searchcontest','txt_groupmember','txt_grp_img','txt_sno','txt_remove','txt_view','txt_memname','txt_group_Accept','txt_groupmember_Accept','txt_accept','txt_reject','txt_groupname','txt_grpowner','txt_grp_img'];
	
	}
	elseif($page_name=="userlist")
	{
	$labelname = ['txt_admingroup','txt_adminrequest','mnu_contestlist','mnu_createcontest','mnu_mycontest','mnu_myhistory','mnu_group','mnu_otherprofile','txt_welcome','txt_menu_logout','txt_myprofile','txt_menu_about',
		'txt_menu_support','txt_menu_terms','txt_menu_privacy','txt_menu_developedby','txt_userlist','txt_back'];	
	}
	elseif($page_name=="editgroup")
	{
	$labelname = ['txt_admingroup','txt_adminrequest','mnu_contestlist','mnu_createcontest','mnu_mycontest','mnu_myhistory','mnu_group','mnu_otherprofile','txt_welcome','txt_menu_logout','txt_myprofile','txt_menu_about',
		'txt_menu_support','txt_menu_terms','txt_menu_privacy','txt_menu_developedby','txt_userlist','txt_back','submnu_editgroup','txt_groupinfo','txt_grouptype','txt_private','txt_open','txt_uploadgroupimage','txt_updategrp'];
	
	}
	
	Session::put('language', $languagename);
	$sessionvalue = Session::get('language');
	return $languageDetails = languageModel::whereIn('ctrlCaptionId',$labelname)->get()->toArray(); 
	}
	public function showlanguagedetails()
	{
	 $page_name = $_POST['page_name'];
	}
}
?>