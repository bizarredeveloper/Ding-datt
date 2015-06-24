package com.bizarre.dingdatt.strings;

import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.util.ArrayList;

public class StringURLs {

	// public static String MAIN = "http://192.168.1.58/dingdatt/";
	public static String MAIN = "http://projects.bizarresoftware.in/dingdatt/webpage/";
	// public static String PARTICIPANT_PHOTO_LOC = MAIN
	// +"public/assets/upload/contest_participant_photo/";
	// public static String GROUP_PHOTO_LOC = MAIN
	// +"public/assets/upload/group/";
	// public static String PROFILE_PHOTO_LOC = MAIN
	// +"public/assets/upload/profile/";
	// public static String SPONSOR_PHOTO_LOC = MAIN
	// +"public/assets/upload/sponsor_photo/";
	// public static String UPLOAD_PARTICIPANT = MAIN
	// +"public/assets/upload/contest_participant_photo/";
	// public static String PROFILE_PIC = MAIN +"public/assets/upload/profile/";

	public static String LOGIN = MAIN + "mobilelogin";
	public static String REGISTRATION = MAIN + "mobileuserregister";
	public static String FACEBOOK_LOGIN = MAIN + "mobilefacebooklogin";
	public static String GOOGLE_LOGIN = MAIN + "mobilegooglelogin";
	public static String CONTEST_LIST = MAIN + "mobilecontestlist";
	public static String CONTEST_INFO = MAIN + "mobilecontestinfo";
	public static String JOIN_CONTEST = MAIN + "joincontest";
	public static String CREATE_CONTEST = MAIN + "mobilecreatecontest";
	public static String UPDATE_CONTEST = MAIN + "mobileupdatecontest";
	public static String PARTICIPANT_DETAILS = MAIN + "participantdetails";
	public static String GET_INTEREST = MAIN + "getinterest";
	public static String GET_CONTEST_FOR_EDIT = MAIN + "getcontestforedit";
	public static String PARTICIPATED_CONTEST = MAIN + "participatedcontest";
	public static String CREATED_CONTEST = MAIN + "createdcontest";
	public static String GROUP_CONTEST = MAIN + "getgrouplistforcontest";
	public static String FOLLOWER_CONTEST = MAIN
			+ "getfollowerlistforinvitecontest";
	public static String INVITE_GROUP_CONTEST = MAIN + "invitegroupsforcontest";
	public static String INVITEGROUPMEMBERFORCONTEST = MAIN
			+ "invitegroupmemberforcontest";
	public static String UNINVITEGROUPMEMBERFORCONTEST = MAIN
			+ "uninvitegroupmemberforcontest";
	public static String UNINVITE_GROUP_CONTEST = MAIN
			+ "uninvitegroupsforcontest";
	public static String INVITE_FOLLOWER_CONTEST = MAIN
			+ "invitefollowerforcontest";
	public static String UNINVITE_FOLLOWER_CONTEST = MAIN
			+ "uninvitefollowerforcontest";
	public static String FORGOT_PASSWORD = MAIN + "forgotpassword";
	public static String USER_PROFILE = MAIN + "getuserprofile";
	public static String CONTEST_GALLERY = MAIN + "contestgallery";
	public static String FOLLOWER = MAIN + "follow";
	public static String UNFOLLOW = MAIN + "unfollow";
	public static String GETCOMMENTS = MAIN + "getcomments";
	public static String COMMENTS = MAIN + "comments";

	public static String GETREPLYCOMMENTS = MAIN + "getreplycomments";
	public static String REPLYCOMMENTS = MAIN + "replycomments";
	public static String VOTING = MAIN + "voting";
	public static String LEADERBOARD = MAIN + "leaderboard";

	public static String VIEWPROFILE = MAIN + "viewprofile";

	public static String MYHISTORY = MAIN + "myhistory";
	public static String GETFOLLOWINGLIST = MAIN + "getfollowinglist";
	public static String GETFOLLOWERLIST = MAIN + "getfollowerlist";
	public static String GETGROUPLIST = MAIN + "getgrouplist";

	public static String GETUSERPROFILE = MAIN + "getuserprofile";
	public static String EDITPROFILE = MAIN + "mobileeditprofile";

	public static String CREATEGROUP = MAIN + "groupcreate";
	public static String UPDATEGROUPDETAILS = MAIN + "updategroupdetails";
	public static String GETGROUPDETAILS = MAIN + "getgroupdetails";

	public static String GETGROUPMEMBERLIST = MAIN + "getgroupmemberlist";
	public static String GETGROUPLISTSEARCH = MAIN + "getgrouplistsearch";
	public static String SEARCHMEMBER = MAIN + "searchmember";

	public static String MEMBEREQUEATTOGROUP = MAIN + "memberequesttogroup";
	public static String ADDMEMBERINTOGROUP = MAIN + "addmemberintogroup";
	public static String UNGROUP = MAIN + "ungroup";
	public static String ADMINREQUEST = MAIN + "getadminrequest";
	public static String MEMBERREQUEST = MAIN + "getmemberrequest";
	public static String NOTIFICATION_COUNT = MAIN + "requestcount";
	public static String ACCEPTGROUPADMINREQUEST = MAIN
			+ "acceptgroupadminrequest";
	public static String REPORTFLAG = MAIN + "reportflag";

	public static String getQuery(String sUrl, ArrayList<String> asName,
			ArrayList<String> asValue) throws UnsupportedEncodingException {
		StringBuilder result = new StringBuilder();
		boolean first = true;

		for (int i = 0; i < asName.size(); i++) {
			if (first)
				first = false;
			else
				result.append("&");

			result.append(URLEncoder.encode(asName.get(i), "UTF-8"));
			result.append("=");
			result.append(URLEncoder.encode(asValue.get(i), "UTF-8"));
		}

		return sUrl + "?" + result.toString();
	}
}
