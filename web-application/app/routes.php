<?php

App::after(function($request, $response)
{
    $response->headers->set('Cache-Control','nocache, no-store, max-age=0, must-revalidate');
    $response->headers->set('Pragma','no-cache');
    $response->headers->set('Expires','Fri, 01 Jan 1990 00:00:00 GMT');
});

/*App::missing(function($exception)
{
    return Redirect::to('/');
}); */



//Login Screens - No need any Authentication, Accessible by All
Route::get('/', 'HomeController@home');
Route::get('/login', 'HomeController@home');
Route::post('/login', 'LoginController@Login');
Route::get('/forgot', 'LoginController@ForgotPassword');
Route::post('forgotpasswordprocess', 'LoginController@ForgotPasswordProcess');
Route::post('ForgotPasswordProcessforadmin','LoginController@ForgotPasswordProcessforadmin');
Route::get('/facebook_login','LoginController@loginWithFacebook');
Route::get('/twitter_login','LoginController@loginWithTwitter');
Route::get('/google_login','LoginController@loginWithGoogle');

/// Admin Login ///
Route::get('admin','LoginController@admin');
Route::post('adminlogin','LoginController@adminLogin');
Route::get('forgotadmin','LoginController@forgotadmin');


//Create User - For Temporary, It was Accessible without Authentication
Route::get('/createuser', 'LoginController@CreateUserLayout');
Route::post('/createuserprocess', 'LoginController@CreateUserProcess');

Route::get('/userregister', function()
{
	//return View::make('user/register/userregister');
});
Route::post('/laravel_register', array('before' => 'csrf', 'uses' => 'RegisterController@laravel_register'));
Route::get('/laravel_register','profileController@profile');

Route::get('/profile','profileController@profile');
//Route::get('carboncall','carboncontroller@carbon1');
//Screens Inside the Array can't be Accessed without Login
Route::group(array('before' => 'auth'), function()
{



//Route::get('/','webpanelController@showpanel');

//// admin ////
Route::get('user','adminController@user');
Route::post('adduser','adminController@adduser');
Route::post('searchuser','adminController@searchuser');
Route::post('activeuser','adminController@activeuser');
Route::get('userdelete','adminController@userdelete');
Route::get('viewusercontest/{data}','adminController@viewusercontest');
Route::get('managecontest','adminController@managecontest');
Route::post('contestsearch','adminController@contestsearch');
Route::get('activecontest','adminController@activecontest');
Route::get('contestparticipantlist/{data}','adminController@contestparticipantlist');
Route::get('removecontestparticipant','adminController@removecontestparticipant');
Route::get('adminviewcontest/{data}','adminController@adminviewcontest');
Route::get('contestdelete','adminController@contestdelete');
Route::get('reportlist','adminController@reportlist');
Route::get('takeactionforreport','adminController@takeactionforreport');
Route::get('regenerateleaderboard','adminController@regenerateleaderboard');
Route::get('category','categoryController@showcategory');
Route::post('activecategory','categoryController@activecategory');
Route::post('addcategory','categoryController@addcategory');
Route::get('gotoeditcategory','categoryController@gotoeditcategory');
Route::post('editcategory/{data}','categoryController@editcategory');
Route::get('deletecategory','categoryController@deletecategory');
Route::get('withoutdeletstakeaction','adminController@withoutdeletstakeaction');


Route::get('getsearchdetails','memberController@getsearchdetails');

//// Webpanel /////////
Route::get('/webpanel','webpanelController@showpanel');
Route::post('/webpanel','webpanelController@showpanel');
//home
Route::get('home', 'HomeController@HomeLayout');
Route::post('/edit_profile_update/{data}','profileController@profileupdate');
Route::get('/edit_profile/{data}','profileController@edit_profiles');
Route::post('/laravel_registeredit/{data}','profileController@profileupdate');
//// Language ////
Route::post('getLanguage','languageController@showlanguagedetails');

Route::get('userdetails','userdetailsController@userdetails');
Route::get('useredit/{data}','userdetailsController@getuserdetails');
//Route::get('userdelete/{data}','userdetailsController@userdetailsdelete');
Route::post('usersearch/{data}','memberController@usersearch');

//Route::post('changeLanguageafterlogin','languageController@getlanguageDetails');


///// Contest /////////
Route::get('contest','contestController@showcontest');
Route::post('contest','contestController@savecontest');
Route::get('contestlist','contestController@showcontentlist');
Route::get('/contest_info/{data}','contestController@showcontestinfo');
Route::get('my_contest','contestController@showmycontest');
Route::post('my_contest','contestController@showmycontest');
Route::get('edit_contest/{data}','contestController@edit_contest');
Route::post('update_contest','contestController@update_contest');
Route::post('join_contest','contestController@join_contest');
Route::get('report','contestController@report');


Route::get('invite_group','ajaxController@invite_group');
Route::post('inviteall_group','ajaxController@inviteall_group');
Route::post('getinviteList','ajaxController@getinviteList');
Route::post('invite_follower','ajaxController@invite_follower');
Route::get('inviteall_follower','ajaxController@inviteall_follower');
Route::get('uninvite_allfollower','ajaxController@uninvite_allfollower');
Route::get('uninvite_group','ajaxController@uninvite_group');
Route::post('uninvite_allgroup','ajaxController@uninvite_allgroup');
Route::get('invitegroupmemberforcontest','ajaxController@invitegroupmemberforcontest');

Route::get('uninvite_group_member','ajaxController@uninvite_group_member');
Route::get('uninviteallgroupmemberforcontest','ajaxController@uninviteallgroupmemberforcontest');


Route::get('uninvite_follower','ajaxController@uninvite_follower');

//Route::get('contestgallery','contestController@contestgallery');

Route::get('showcontestlistdetails/{data}','contestController@showcontentlistget');
Route::post('showcontestlistdetails/{data}','contestController@showcontentlistdetails');

Route::get('redirectsoial','groupController@redirectsoial');

//// For Responsive Menu /////////
Route::get('contestinforesponsive','contestController@contestinforesponsive');
Route::get('contestinfosubtabresponsive','contestController@contestinfosubtabresponsive');
Route::get('otherprofileresponsive','profileController@otherprofileresponsive');
Route::get('mycontestresponsive','contestController@mycontestresponsive');
Route::get('groupresponsive','groupController@groupresponsive');



///// Create Group //////
Route::get('group','groupController@showgroup');
Route::post('groupsearch','groupController@showgroupwithsearch');
Route::post('group','groupController@creategroupinweb');
Route::get('groupdelete','groupController@groupdelete');
Route::get('groupmemberdelete','groupController@groupmemberdelete');
Route::get('editgroup/{data}','groupController@editgroup');
Route::post('editgroup/{data}','groupController@updategroup');
Route::get('exitgroup','groupController@exitgroup');
Route::get('activegroup','groupController@activegroup');
Route::get('sharegroup/{data}','groupController@sharegroup');

///// add member to group /////////
Route::get('addmembertogroup/{data}','memberController@addmembertogroup');
Route::get('addthismembertogroup','groupController@addthismembertogroup');
//Route::get('privategroupaccept','groupController@privategroupaccept');
Route::get('ajaxaccepgroup','ajaxController@ajaxaccepgroup');
Route::get('ajaxrejectgroup','ajaxController@ajaxrejectgroup');

///// Common ///////////
Route::get('getmultilingualalert','ajaxController@getmultilingualalert');

///// Group Member or view Group ///////
Route::get('viewgroupmember/{data}','groupController@viewgroupmember');
Route::get('viewgroupmemberfrominvite','groupController@viewgroupmemberfrominvite');
Route::get('joinintogroup/{data}','groupController@joinintogroup');
Route::get('groupmemberback','memberController@groupmemberback');

Route::get('other_profile/{data}','profileController@other_profile');
Route::get('follow','followController@putfollow');
Route::get('putfollowinotherprofile','followController@putfollowinotherprofile');
Route::get('contesttab','contestController@contesttab');
Route::get('contesttab_close','contestController@contesttab_close');
Route::get('contesttabeithreport','contestController@contesttabeithreport');
Route::get('contesttabwithoutid','contestController@contesttabwithoutid');
Route::get('viewcomment','commentController@viewcomment');
Route::get('putcomment','commentController@putcomment');
Route::get('putreplycomment','commentController@putreplycomment');

Route::get('accepttherequest','groupController@accepttherequest');


///// Follow ///////
Route::get('unfollowforweb','followController@unfollowforweb');


///// Voting ///////
Route::get('voting','votingController@voting');


//Logout Screen at Last
Route::get('logout', 'LoginController@Logout');
Route::get('adminlogout','LoginController@adminlogout'); 

Route::get('contest_info1/{data}','contestController@showcontestinfo1');

});
/////
Route::get('leaderboardgenerate','leaderboardgenerateController@leaderboardgenerate');


Route::controller('password', 'RemindersController');

Route::resource('password', 'RemindersController', array(
    'only' => array('index', 'store', 'show', 'update')
));

////// Language Change ////////////////
Route::post('changeLanguage','languageController@getlanguageDetails');
Route::post('loadcontest_list','ajaxController@getcontestList');


////// Webservices //////////



Route::post('mobilelogin','webservice3Controller@mobilelogin');
Route::post('mobileuserregister','webservice3Controller@mobileregister');
Route::post('getuserprofile','webservice3Controller@getuserprofile');
Route::post('mobileeditprofile','webservice3Controller@editmyprofile');
Route::post('mobilecreatecontest','webservice3Controller@createcontestmobile');
Route::post('getcontestforedit','webservice3Controller@getcontestmobile');
Route::post('mobileupdatecontest','webservice3Controller@updatecontestmobile');
Route::post('mobilecontestlist','webservice3Controller@getcontestlistmobile');
Route::post('mobilecontestinfo','webservice3Controller@getcontestinfo');
Route::post('joincontest','webservice3Controller@joincontest');
Route::post('contestgallery','webservice3Controller@contestgallery');
Route::post('participantdetails','webservice3Controller@participantdetails');
Route::post('getcontestforvoting','webservice3Controller@getcontestforvoting');
Route::post('voting','webservice3Controller@voting');
Route::post('leaderboard','webservice3Controller@leaderboard');
Route::post('follow','webservice3Controller@follower');
Route::post('getfollowerlist','webservice3Controller@getfollowers');
Route::post('getcomments','webservice3Controller@getcommentsdetails');
Route::post('comments','webservice3Controller@putcomments');
Route::post('myhistory','webservice3Controller@myhistory');
Route::post('getfollowinglist','webservice3Controller@getfollowinglist');
Route::post('viewprofile','webservice3Controller@viewprofile');
Route::post('mobilemultilingual','webservice3Controller@mobilemultilingual');
Route::get('mobilegetlanguages','webservice3Controller@mobilegetlanguages');
Route::post('groupcreate','webservice3Controller@creategroup');
Route::post('getgroupdetails','webservice3Controller@getgroupdetails');
Route::post('updategroupdetails','webservice3Controller@updategroupdetails');
Route::post('getgrouplist','webservice3Controller@getgrouplist');
Route::post('getgrouplistforcontest','webservice3Controller@getgrouplistforcontest');
Route::post('getgroupmemberlist','webservice3Controller@getgroupmemberlist');
Route::post('ungroup','webservice3Controller@ungroup');
Route::post('unfollow','webservice3Controller@unfollow');
Route::post('mobilefacebooklogin','webservice3Controller@facebooklogin');
Route::post('mobilegooglelogin','webservice3Controller@mobilegooglelogin');
Route::post('replycomments','webservice3Controller@replycomments');
Route::post('getreplycomments','webservice3Controller@getreplycomments');
Route::post('invitegroupsforcontest','webservice3Controller@invitegroupsforcontest');
Route::post('invitegroupmemberforcontest','webservice3Controller@invitegroupmemberforcontest');
Route::post('invitefollowerforcontest','webservice3Controller@invitefollowesforcontest');
Route::post('uninvitefollowerforcontest','webservice3Controller@uninvitefollowerforcontest');
Route::post('getfollowerlistforinvitecontest','webservice3Controller@getfollowerlistforinvitecontest');
Route::post('forgotpassword','webservice3Controller@forgotpassword');
Route::get('getinterest','webservice3Controller@getinterest');
Route::post('participatedcontest','webservice3Controller@participatedcontest');
Route::post('createdcontest','webservice3Controller@createdcontest');
Route::post('getgrouplistsearch','webservice3Controller@getgrouplistsearch');
Route::post('getadminrequest','webservice3Controller@getadminrequest');
Route::post('getmemberrequest','webservice3Controller@getmemberrequest');
Route::post('requestcount','webservice3Controller@requestcount');
Route::post('reportflag','webservice3Controller@reportflag');
Route::post('uninvitegroupsforcontest','webservice3Controller@uninvitegroupsforcontest');
Route::post('uninvitegroupmemberforcontest','webservice3Controller@uninvitegroupmemberforcontest');

//Route::post('dropbox','dropboxController@dropbox');
Route::post('creategroupmembers','webservicesController@addgroupmembers');
Route::post('grouplist','webservicesController@getgrouplist');


Route::post('addmemberintogroup','webservice3Controller@addmemberintogroup');
Route::post('acceptgroupadminrequest','webservice3Controller@groupmemberaccepttheadminrequest');
Route::post('memberequesttogroup','webservice3Controller@memberequesttogroup');
Route::post('searchmember','webservice3Controller@searchmember');

//Route::post('mobileimage','webservicesController@imagesave');
//Route::post('addmembersingroup','webservice3Controller@addmembersforgroup');

Route::get('dropbox','dropboxController@dropbox');