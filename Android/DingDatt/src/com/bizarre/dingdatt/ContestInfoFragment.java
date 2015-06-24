package com.bizarre.dingdatt;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Date;
import java.util.HashMap;

import org.json.JSONArray;
import org.json.JSONObject;

import twitter4j.StatusUpdate;
import twitter4j.Twitter;
import twitter4j.TwitterException;
import twitter4j.TwitterFactory;
import twitter4j.User;
import twitter4j.auth.AccessToken;
import twitter4j.auth.RequestToken;
import twitter4j.conf.Configuration;
import twitter4j.conf.ConfigurationBuilder;

import com.bizarre.dingdatt.adapter.InviteAdapter;
import com.bizarre.dingdatt.adapter.LeaderBoardAdapter;
import com.bizarre.dingdatt.database.DingDattDatabase;
import com.bizarre.dingdatt.imageloader.ImageLoader;
import com.bizarre.dingdatt.pojo.ContestDetails;
import com.bizarre.dingdatt.pojo.GroupFollower;
import com.bizarre.dingdatt.pojo.LeaderBoardpojo;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;
import com.bizarre.dingdatt.twitter.WebviewActivity;
import com.facebook.FacebookRequestError;
import com.facebook.Request;
import com.facebook.Response;
import com.facebook.Session;
import com.facebook.model.GraphObject;
import com.facebook.widget.LoginButton;
import com.houcine.tumblr.library.TumblrLoginActivity;
import com.tumblr.jumblr.JumblrClient;
import com.tumblr.jumblr.types.Photo;
import com.tumblr.jumblr.types.PhotoPost;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.drawable.BitmapDrawable;
import android.graphics.drawable.Drawable;
import android.media.MediaMetadataRetriever;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Environment;
import android.os.StrictMode;
import android.provider.MediaStore;
import android.provider.MediaStore.Images;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ImageView;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;

/**
 * To display selected contest information and perform to join, invite, share
 * and voting.
 * 
 * @author karthik
 *
 */
public class ContestInfoFragment extends Fragment {

	private static FragmentActivity context;

	private LinearLayout content;
	private View rootView;
	LayoutInflater inflater;
	ImageView view_gallery;
	TextView view_topic;

	TextView title1;
	TextView title2;
	LinearLayout instant_share;

	String view_img_loc = "";
	ScrollView view_gallery_layout;

	ScrollView contest_description;
	ScrollView contest_join;
	TextView leaderboard_message;
	// LinearLayout contest_view;
	ScrollView image_submit;
	LinearLayout listview_layout;

	LinearLayout join_layout;
	LinearLayout share_layout;
	LinearLayout vote_layout;
	LinearLayout invite_layout;
	LinearLayout group_layout;
	LinearLayout gallery_layout;
	LinearLayout leaderboard_layout;
	LinearLayout follower_layout;
	Button invite_all;

	ImageView show_image;
	TextView show_topic;
	LinearLayout show_image_layout;
	ImageView take_snap;
	ImageView get_gallery;

	Button choose_other;
	Button submit;

	TextView facebook;
	boolean bfacebook = false;
	TextView twitter;
	boolean btwitter = false;
	private static Twitter mTwitter;
	private static RequestToken requestToken;
	TextView tumblr;
	boolean bTumblr = false;
	TextView flickr;

	TextView contest_start_date;
	TextView contest_end_date;
	TextView voting_start_date;
	TextView voting_end_date;
	TextView prize;
	TextView contes_name;

	ImageView creatorimage;
	TextView creatorname;

	ImageView contest_image;
	TextView sponsorname;
	LinearLayout sponsorlayout;
	TextView description;
	ImageView sponsorphoto;

	ListView listview;
	String PHOTO = "p";
	String VIDEO = "v";
	String TOPIC = "t";

	private static int SELECT_IMAGE = 30;
	private static int SELECT_VIDEO = 31;

	ContestDetails contestdetails2 = null;
	ArrayList<GroupFollower> mGroup = new ArrayList<GroupFollower>();
	ArrayList<GroupFollower> mFollower = new ArrayList<GroupFollower>();
	int contest_id;
	String file_name = "";

	/* Shared preference keys */
	private static final String PREF_NAME = "sample_twitter_pref";
	private static final String PREF_KEY_OAUTH_TOKEN = "oauth_token";
	private static final String PREF_KEY_OAUTH_SECRET = "oauth_token_secret";
	private static final String PREF_KEY_TWITTER_LOGIN = "is_twitter_loggedin";
	private static final String PREF_USER_NAME = "twitter_user_name";

	private static final String TUMBLR_CONSUMER_KEY = "dNATcL5N9ocXofLufyswQZ2wzqAvGAanxocymUGV2DIy8VOhLD";
	private static final String TUMBLR_CONSUMER_SECRET = "d024cGZTeM4EoB2uEM5Zr4WRsOUxSALKxX0cqvt1Bexe9luAuv";
	private static final int REQUEST_CODE_TUMBLR_LOGIN = 5593;

	String consumerKey = "";
	String consumerSecret = "";
	String callbackUrl = "";
	String oAuthVerifier = "";
	String contestType = "";

	String topic_photo = "";
	String topic_video = "";

	ArrayList<LeaderBoardpojo> leaderBoardpojos = new ArrayList<LeaderBoardpojo>();

	/* Any number for uniquely distinguish your request */
	public static final int WEBVIEW_REQUEST_CODE = 100;

	public ContestInfoFragment(FragmentActivity context, int contest_id) {

		this.context = context;
		this.contest_id = contest_id;
	}

	LoginButton fb2;

	/**
	 * Check facebook session is opened or closed
	 * 
	 * @return if opened to retrun true else return false.
	 */
	private boolean CheckFacebookSession() {
		Session session = Session.getActiveSession();
		return (session != null && session.isOpened());
	}

	@Override
	public View onCreateView(final LayoutInflater inflater1,
			ViewGroup container, Bundle savedInstanceState) {
		this.inflater = inflater1;
		rootView = inflater.inflate(R.layout.contest_info, container, false);

		Main.SetAdvertisment(rootView);

		initTwitterConfigs();

		StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder()
				.permitAll().build();
		StrictMode.setThreadPolicy(policy);

		TextView join = (TextView) rootView.findViewById(R.id.join);
		TextView share = (TextView) rootView.findViewById(R.id.share);
		TextView vote = (TextView) rootView.findViewById(R.id.vote);
		TextView invite = (TextView) rootView.findViewById(R.id.invite);
		TextView group = (TextView) rootView.findViewById(R.id.group);
		TextView follower = (TextView) rootView.findViewById(R.id.follower);
		TextView gallery = (TextView) rootView.findViewById(R.id.gallery);
		TextView leaderboard = (TextView) rootView
				.findViewById(R.id.leaderboard);

		contest_start_date = (TextView) rootView
				.findViewById(R.id.contest_start_date);
		contest_end_date = (TextView) rootView
				.findViewById(R.id.contest_end_date);
		voting_start_date = (TextView) rootView
				.findViewById(R.id.voting_start_date);
		voting_end_date = (TextView) rootView
				.findViewById(R.id.voting_end_date);
		prize = (TextView) rootView.findViewById(R.id.prize);
		contes_name = (TextView) rootView.findViewById(R.id.contes_name);
		contest_image = (ImageView) rootView.findViewById(R.id.contest_image);

		join_layout = (LinearLayout) rootView.findViewById(R.id.join_layout);
		share_layout = (LinearLayout) rootView.findViewById(R.id.share_layout);
		vote_layout = (LinearLayout) rootView.findViewById(R.id.vote_layout);
		invite_layout = (LinearLayout) rootView
				.findViewById(R.id.invite_layout);
		group_layout = (LinearLayout) rootView.findViewById(R.id.group_layout);
		gallery_layout = (LinearLayout) rootView
				.findViewById(R.id.gallery_layout);
		leaderboard_layout = (LinearLayout) rootView
				.findViewById(R.id.leaderboard_layout);
		follower_layout = (LinearLayout) rootView
				.findViewById(R.id.follower_layout);

		gallery.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				leaderboard_message.setVisibility(View.GONE);

				if (leaderboard_layout.getVisibility() == View.VISIBLE) {

					Intent intent = new Intent(context,
							ContestGalleryActivity.class);
					intent.putExtra("contest_id", Integer.toString(contest_id));
					intent.putExtra("name", contestdetails2.getContest_name());
					intent.putExtra("voting", false);
					intent.putExtra("type", contestType);
					startActivity(intent);

				} else {
					view_gallery_layout.setVisibility(View.VISIBLE);
					contest_description.setVisibility(View.GONE);
					contest_join.setVisibility(View.GONE);
					image_submit.setVisibility(View.GONE);
					listview_layout.setVisibility(View.GONE);

					GetParticipantGalleryDetails();
				}
			}
		});

		leaderboard.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				leaderboard_message.setVisibility(View.GONE);

				view_gallery_layout.setVisibility(View.GONE);
				contest_description.setVisibility(View.GONE);
				contest_join.setVisibility(View.GONE);
				image_submit.setVisibility(View.GONE);
				listview_layout.setVisibility(View.VISIBLE);
				invite_all.setVisibility(View.GONE);

				GetLeaderboardDetails();
			}
		});

		vote.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				try {

					leaderboard_message.setVisibility(View.GONE);

					SQLiteDatabase database = SQLiteDatabase.openDatabase(
							DingDattDatabase.DATABASE_NAME, null,
							SQLiteDatabase.OPEN_READWRITE);

					LocalData data = new LocalData(context);

					String selquery = "SELECT * FROM "
							+ DingDattDatabase.TABLE_GALLERY + " WHERE "
							+ DingDattDatabase.FIELD_GALLERY_CONTEST_ID
							+ " = '" + contest_id + "' AND "
							+ DingDattDatabase.FIELD_GALLERY_MY_USER_ID
							+ " = '" + data.GetS("userid") + "'";

					Cursor cursor = database.rawQuery(selquery, null);
					cursor.moveToFirst();

					if (cursor.getCount() == 0) {
						cursor.close();
						database.close();

						GetContestGallery();
					} else {
						cursor.close();
						database.close();

						Intent intent = new Intent(context,
								ContestGalleryActivity.class);
						intent.putExtra("contest_id",
								Integer.toString(contest_id));
						intent.putExtra("name",
								contestdetails2.getContest_name());
						intent.putExtra("voting", true);
						intent.putExtra("type", contestType);
						startActivity(intent);
						context.finish();
					}
				} catch (Exception exp) {

					Log.d("ERROR", "ERROR " + exp.getMessage());
				}
			}
		});

		join.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				leaderboard_message.setVisibility(View.GONE);

				if (contestType.equalsIgnoreCase(TOPIC)) {

					view_gallery_layout.setVisibility(View.GONE);
					gallery_layout.setVisibility(View.GONE);
					contest_description.setVisibility(View.VISIBLE);
					image_submit.setVisibility(View.GONE);
					listview_layout.setVisibility(View.GONE);
					view_gallery_layout.setVisibility(View.GONE);

					Intent intent = new Intent(context,
							TopicDialogActivity.class);
					startActivityForResult(intent, 5);

				} else {
					view_gallery_layout.setVisibility(View.GONE);
					gallery_layout.setVisibility(View.GONE);
					contest_description.setVisibility(View.GONE);
					contest_join.setVisibility(View.VISIBLE);
					// contest_view.setVisibility(View.GONE);
					image_submit.setVisibility(View.GONE);
					listview_layout.setVisibility(View.GONE);
					view_gallery_layout.setVisibility(View.GONE);
				}
			}
		});

		share.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				String type = "";

				leaderboard_message.setVisibility(View.GONE);

				if (contestType.equalsIgnoreCase("p") == true)
					type = "Photo";
				else if (contestType.equalsIgnoreCase("v") == true)
					type = "Video";
				else if (contestType.equalsIgnoreCase("t") == true)
					type = "Topic";

				String sURL = StringURLs.MAIN + "contest_info/" + contest_id;
				String name = "Contest Name:"
						+ contestdetails2.getContest_name();
				// String type = "Contest type:" + contestType;
				String handled = "Organised by:"
						+ creatorname.getText().toString();

				Intent intent = new Intent();
				intent.setAction(Intent.ACTION_SEND);

				intent.setType("text/plain");
				intent.putExtra(Intent.EXTRA_TEXT, sURL + "\n" + name + "\n"
						+ "Contest type:" + type + "\n" + handled);

				startActivityForResult(Intent.createChooser(intent, "Share"),
						SHARE);
			}
		});

		invite.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				leaderboard_message.setVisibility(View.GONE);

				contest_description.setVisibility(View.VISIBLE);
				contest_join.setVisibility(View.GONE);
				// contest_view.setVisibility(View.GONE);
				image_submit.setVisibility(View.GONE);
				listview_layout.setVisibility(View.GONE);
				join_layout.setVisibility(View.GONE);
				share_layout.setVisibility(View.GONE);
				group_layout.setVisibility(View.VISIBLE);
				follower_layout.setVisibility(View.VISIBLE);
				share_layout.setVisibility(View.GONE);
				invite_layout.setVisibility(View.GONE);
				gallery_layout.setVisibility(View.GONE);
				view_gallery_layout.setVisibility(View.GONE);
			}
		});

		group.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				leaderboard_message.setVisibility(View.GONE);

				contest_description.setVisibility(View.GONE);
				contest_join.setVisibility(View.GONE);
				// contest_view.setVisibility(View.GONE);
				image_submit.setVisibility(View.GONE);
				listview_layout.setVisibility(View.VISIBLE);

				view_gallery_layout.setVisibility(View.VISIBLE);
				GetGroupFollowerList(GROUP);
				view_gallery_layout.setVisibility(View.GONE);
			}
		});

		follower.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				leaderboard_message.setVisibility(View.GONE);

				contest_description.setVisibility(View.GONE);
				contest_join.setVisibility(View.GONE);
				// contest_view.setVisibility(View.GONE);
				image_submit.setVisibility(View.GONE);
				listview_layout.setVisibility(View.VISIBLE);

				GetGroupFollowerList(FOLLOWER);
				view_gallery_layout.setVisibility(View.GONE);
			}
		});

		content = (LinearLayout) rootView.findViewById(R.id.content);

		View child = inflater1.inflate(R.layout.content_description, null);
		contest_description = (ScrollView) child
				.findViewById(R.id.contest_description);
		leaderboard_message = (TextView) child.findViewById(R.id.leaderboard);
		contest_join = (ScrollView) child.findViewById(R.id.contest_join);
		image_submit = (ScrollView) child.findViewById(R.id.image_submit);
		listview_layout = (LinearLayout) child
				.findViewById(R.id.listview_layout);
		listview = (ListView) child.findViewById(R.id.listview);
		listview.setTag("-1");

		view_gallery_layout = (ScrollView) child
				.findViewById(R.id.view_gallery_layout);
		view_gallery = (ImageView) child.findViewById(R.id.view_gallery);
		view_topic = (TextView) child.findViewById(R.id.view_topic);

		title1 = (TextView) child.findViewById(R.id.title1);
		title2 = (TextView) child.findViewById(R.id.title2);
		instant_share = (LinearLayout) child.findViewById(R.id.instant_sharing);

		view_gallery.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				Intent intent = new Intent(context, ViewGalleryActivity.class);
				intent.putExtra("type", contestType);
				intent.putExtra("loc", view_img_loc);
				startActivity(intent);
			}
		});

		creatorimage = (ImageView) child.findViewById(R.id.creatorimage);
		creatorname = (TextView) child.findViewById(R.id.creatorname);
		invite_all = (Button) child.findViewById(R.id.invite_all);

		listview.setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int pos,
					long arg3) {
				// TODO Auto-generated method stub

				String tag = listview.getTag().toString();

				if (tag.equalsIgnoreCase(LEADER_BOARD) == true) {

					if (leaderBoardpojos.size() > 0
							&& leaderBoardpojos.get(pos).getUser_id().length() > 0) {
						Intent intent = new Intent(context,
								MyProfileActivity.class);
						intent.putExtra("userid", leaderBoardpojos.get(pos)
								.getUser_id());
						startActivity(intent);
					}

				} else if (tag.equalsIgnoreCase("0") == true) {

					Intent intent = new Intent(context,
							MemberInviteActivity.class);
					intent.putExtra("groupid", mGroup.get(pos).getGroupid());
					intent.putExtra("groupname", mGroup.get(pos).getGroupname());
					intent.putExtra("contestid", contest_id);
					startActivityForResult(intent, 20);

				} else if (tag.equalsIgnoreCase("1") == true) {

					if (mFollower.get(pos).getInvite() == 0) {
						String[] alist = { (mFollower.get(pos).getFollowerid() + "") };

						InviteUninviteFollower(alist, INVITE);
					} else if (mFollower.get(pos).getInvite() > 0) {
						String[] alist = { (mFollower.get(pos).getFollowerid() + "") };

						InviteUninviteFollower(alist, UNINVITE);
					}
				}
			}
		});

		sponsorname = (TextView) child.findViewById(R.id.sponsorname);
		sponsorlayout = (LinearLayout) child.findViewById(R.id.sponserlayout);
		description = (TextView) child.findViewById(R.id.description);
		sponsorphoto = (ImageView) child.findViewById(R.id.sponsorphoto);

		take_snap = (ImageView) child.findViewById(R.id.take_snap);
		get_gallery = (ImageView) child.findViewById(R.id.get_gallery);
		show_image = (ImageView) child.findViewById(R.id.show_image);
		show_image_layout = (LinearLayout) child
				.findViewById(R.id.show_image_layout);
		show_topic = (TextView) child.findViewById(R.id.show_topic);

		show_topic.setVisibility(View.GONE);

		choose_other = (Button) child.findViewById(R.id.choose_other);
		submit = (Button) child.findViewById(R.id.submit);

		choose_other.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (contestType.equalsIgnoreCase(TOPIC)) {
					Intent intent = new Intent(context,
							TopicDialogActivity.class);
					intent.putExtra("topic", show_topic.getText().toString());
					startActivityForResult(intent, 5);
				} else {
					contest_description.setVisibility(View.GONE);
					contest_join.setVisibility(View.VISIBLE);
					// contest_view.setVisibility(View.GONE);
					image_submit.setVisibility(View.GONE);
					listview_layout.setVisibility(View.GONE);
				}
			}
		});

		submit.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (contestType.equalsIgnoreCase(TOPIC) != true) {
					JoinContest(0);

					if (contestType.equalsIgnoreCase(VIDEO) != true
							&& contestType.equalsIgnoreCase(TOPIC) != true) {
						if (CheckFacebookSession() == true && bfacebook == true) {
							onClickPostPhoto();
						}

						if (bTumblr == true) {

							LocalData localData = new LocalData(context);

							new TumblrPost().execute(TUMBLR_CONSUMER_KEY,
									TUMBLR_CONSUMER_SECRET,
									localData.GetS("tumblr_token"),
									localData.GetS("tumblr_token_secret"),
									file_name, localData.GetS("blog_name"));
						}

						if (btwitter == true) {

							String type = "";

							if (contestType.equalsIgnoreCase("p") == true)
								type = "Photo";
							else if (contestType.equalsIgnoreCase("v") == true)
								type = "Video";
							else if (contestType.equalsIgnoreCase("t") == true)
								type = "Topic";

							String sURL = StringURLs.MAIN + "contest_info/"
									+ contest_id;
							String name = "Contest Name:"
									+ contestdetails2.getContest_name();
							// String type = "Contest type:" + contestType;
							String handled = "Organised by:"
									+ creatorname.getText().toString();

							new updateTwitterStatus().execute(sURL + "\n"
									+ name + "\n" + "Contest type:" + type
									+ "\n" + handled);
						}
					}
				} else {
					JoinContest(1);
				}
			}
		});

		facebook = (TextView) child.findViewById(R.id.facebook);
		twitter = (TextView) child.findViewById(R.id.twitter);
		tumblr = (TextView) child.findViewById(R.id.tumblr);
		flickr = (TextView) child.findViewById(R.id.flickr);

		fb2 = (LoginButton) child.findViewById(R.id.fb2);
		fb2.setPublishPermissions(Arrays.asList("publish_actions"));

		facebook.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				// click

				if (bfacebook == false) {

					if (CheckFacebookSession() == false) {
						fb2.performClick();
					} else if (CheckFacebookSession() == true) {

						facebook.setCompoundDrawablesWithIntrinsicBounds(
								R.drawable.facebook_blue, 0, 0, 0);
						facebook.setTextColor(Color.rgb(57, 89, 156));

						bfacebook = true;
					}
				} else {

					facebook.setCompoundDrawablesWithIntrinsicBounds(
							R.drawable.facebook_gray, 0, 0, 0);
					facebook.setTextColor(Color.rgb(0, 0, 0));

					bfacebook = false;
				}
			}
		});

		twitter.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				// ChangeView(v);

				try {
					SharedPreferences sharedPreferences = context
							.getSharedPreferences(LocalData.MY_PREFS_NAME, 0);
					boolean isLoggedIn = sharedPreferences.getBoolean(
							PREF_KEY_TWITTER_LOGIN, false);

					if (btwitter == false) {

						if (!isLoggedIn)
							loginToTwitter();
						else {
							twitter.setCompoundDrawablesWithIntrinsicBounds(
									R.drawable.twitter_blue, 0, 0, 0);
							twitter.setTextColor(Color.rgb(57, 89, 156));
							btwitter = true;
						}
					} else {
						twitter.setCompoundDrawablesWithIntrinsicBounds(
								R.drawable.twitter_gray, 0, 0, 0);
						twitter.setTextColor(Color.rgb(0, 0, 0));
						btwitter = false;
					}
				} catch (Exception exp) {

				}

			}
		});

		tumblr.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {

				if (bTumblr == false) {
					if (!TumblrLoginActivity.isConnected(context)) {

						Intent tumblrLoginIntent = new Intent(context,
								TumblrLoginActivity.class);
						tumblrLoginIntent.putExtra(
								TumblrLoginActivity.TUMBLR_CONSUMER_KEY,
								TUMBLR_CONSUMER_KEY);
						tumblrLoginIntent.putExtra(
								TumblrLoginActivity.TUMBLR_CONSUMER_SECRET,
								TUMBLR_CONSUMER_SECRET);
						startActivityForResult(tumblrLoginIntent,
								REQUEST_CODE_TUMBLR_LOGIN);
					} else {
						tumblr.setCompoundDrawablesWithIntrinsicBounds(
								R.drawable.tumblr_blue, 0, 0, 0);
						tumblr.setTextColor(Color.rgb(57, 89, 156));

						bTumblr = true;
					}
				} else {
					tumblr.setCompoundDrawablesWithIntrinsicBounds(
							R.drawable.tumblr_gray, 0, 0, 0);
					tumblr.setTextColor(Color.rgb(0, 0, 0));
					bTumblr = false;
				}
			}
		});

		take_snap.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (contestType.equalsIgnoreCase(PHOTO)) {
					openCamera();
				} else if (contestType.equalsIgnoreCase(VIDEO)) {
					openVideo();
				}
			}
		});

		get_gallery.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (contestType.equalsIgnoreCase(PHOTO)) {
					Intent i = new Intent(
							Intent.ACTION_PICK,
							android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
					startActivityForResult(i, SELECT_IMAGE);
				} else {
					Intent i = new Intent(
							Intent.ACTION_PICK,
							android.provider.MediaStore.Video.Media.EXTERNAL_CONTENT_URI);
					startActivityForResult(i, SELECT_VIDEO);
				}
			}
		});

		contest_description.setVisibility(View.VISIBLE);
		contest_join.setVisibility(View.GONE);
		image_submit.setVisibility(View.GONE);

		content.addView(child);

		GetContestInfoDetails(Integer.toString(contest_id));

		return rootView;
	}

	/**
	 * Get Contest Gallery from server
	 */
	private void GetContestGallery() {
		try {

			String url = StringURLs.CONTEST_GALLERY;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("contest_id");
			names.add("timezone");
			names.add("user_id");

			LocalData data = new LocalData(context);

			values.add(Integer.toString(contest_id));
			values.add(Main.GetTimeZone());
			values.add(data.GetS("userid"));

			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(context);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					UpdateContestGalleryToDatabase(sJSON);

					Intent intent = new Intent(context,
							ContestGalleryActivity.class);

					intent.putExtra("contest_id", Integer.toString(contest_id));
					intent.putExtra("name", contestdetails2.getContest_name());
					intent.putExtra("voting", true);
					intent.putExtra("type", contestType);
					startActivity(intent);
					context.finish();
				}
			});

			connectServerImage.setMode(ConnectServerImage.MODE_POST);
			connectServerImage.setNames(names);
			connectServerImage.setValues(values);

			System.out.println(url);

			connectServerImage.execute(url);

		} catch (Exception exp) {

		}
	}

	/**
	 * Get Leader board Details.
	 */
	private void GetLeaderboardDetails() {

		String url = StringURLs.LEADERBOARD;

		ArrayList<String> names = new ArrayList<String>();
		ArrayList<String> values = new ArrayList<String>();

		names.add("contest_id");
		names.add("timezone");

		values.add(Integer.toString(contest_id));
		values.add(Main.GetTimeZone());

		ConnectServerImage connectServerImage = new ConnectServerImage();
		connectServerImage.setContext(context);
		connectServerImage.setListener(new ConnectServerListener() {

			@Override
			public void onServerResponse(String sJSON, JSONObject jsonObject) {

				try {

					if (sJSON.length() > 0) {

						JSONObject jsonObject2 = new JSONObject(sJSON);

						if (jsonObject2.getJSONObject("response")
								.getString("success").equalsIgnoreCase("1") == true) {

							JSONArray VotingResult = jsonObject2
									.getJSONArray("Voting Result");

							leaderBoardpojos = new ArrayList<LeaderBoardpojo>();

							if (VotingResult.length() == 0) {

								leaderboard_message.setVisibility(View.VISIBLE);
								return;
							}

							LeaderBoardpojo boardpojo1 = new LeaderBoardpojo();

							boardpojo1.setId("0");
							boardpojo1.setName("Contestant Name");

							if (contestType.equalsIgnoreCase("p") == true) {
								boardpojo1.setPicture("Image");
							} else if (contestType.equalsIgnoreCase("v") == true) {
								boardpojo1.setPicture("Video");
							} else if (contestType.equalsIgnoreCase("t") == true) {
								boardpojo1.setPicture("Topic");
							}

							boardpojo1.setUser_id("");
							boardpojo1.setPosition("Rank");
							boardpojo1.setVotes("No. of Dings");

							leaderBoardpojos.add(boardpojo1);

							for (int i = 0; i < VotingResult.length(); i++) {
								JSONObject jsonObject3 = VotingResult
										.getJSONObject(i);

								LeaderBoardpojo boardpojo = new LeaderBoardpojo();

								boardpojo.setId(jsonObject3.getString("ID"));
								boardpojo.setName(jsonObject3.getString("name"));

								if (contestType.equalsIgnoreCase("t") == true) {

									boardpojo.setPicture(jsonObject3
											.getString("uploadtopic"));
								} else {

									boardpojo.setPicture(jsonObject3
											.getString("uploadfile"));
								}

								boardpojo.setPosition(jsonObject3
										.getString("position"));
								boardpojo.setVotes(jsonObject3
										.getString("votes"));
								boardpojo.setUser_id(jsonObject3
										.getString("user_id"));

								leaderBoardpojos.add(boardpojo);
							}

							LeaderBoardAdapter adapter = new LeaderBoardAdapter(
									context, leaderBoardpojos, contestType);
							listview.setTag(LEADER_BOARD);
							listview.setAdapter(adapter);

						} else {
							String msgcode = jsonObject.getJSONObject(
									"response").getString("msgcode");

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											msgcode), Toast.LENGTH_LONG).show();
						}

					} else {

						Toast.makeText(context,
								Main.getStringResourceByName(context, "c100"),
								Toast.LENGTH_LONG).show();
					}

				} catch (Exception exp) {

					Toast.makeText(context,
							Main.getStringResourceByName(context, "c100"),
							Toast.LENGTH_LONG).show();
				}

			}
		});

		connectServerImage.setMode(ConnectServerImage.MODE_POST);
		connectServerImage.setNames(names);
		connectServerImage.setValues(values);
		connectServerImage.execute(url);
	}

	public static String LEADER_BOARD = "leaderboard";

	/**
	 * Get Contest gallery and load into local database(TABLE - gallery)
	 * 
	 * @param sJson
	 */
	private void UpdateContestGalleryToDatabase(String sJson) {

		SQLiteDatabase database;

		try {

			if (sJson.length() > 0) {
				database = SQLiteDatabase.openDatabase(
						DingDattDatabase.DATABASE_NAME, null,
						SQLiteDatabase.OPEN_READWRITE);

				JSONObject jsonObject = new JSONObject(sJson);

				String success = jsonObject.getJSONObject("response")
						.getString("success");

				if (success.equalsIgnoreCase("1") == true) {

					JSONArray participantlist = jsonObject
							.getJSONArray("participantlist");

					for (int i = 0; i < participantlist.length(); i++) {

						JSONObject object = participantlist.getJSONObject(i);

						LocalData data = new LocalData(context);

						if (object.getInt("vote") == 0) {
							String query = "INSERT INTO "
									+ DingDattDatabase.TABLE_GALLERY
									+ "("
									+ DingDattDatabase.FIELD_GALLERY_CONTEST_ID
									+ ", "
									+ DingDattDatabase.FIELD_GALLERY_ID
									+ ", "
									+ DingDattDatabase.FIELD_GALLERY_UPLOADDATE
									+ ", "
									+ DingDattDatabase.FIELD_GALLERY_UPLOADFILE
									+ ", "
									+ DingDattDatabase.FIELD_GALLERY_UPLOADTOPIC
									+ ", "

									+ DingDattDatabase.FIELD_GALLERY_PROFILEPICTURE
									+ ", "
									+ DingDattDatabase.FIELD_GALLERY_FOLLOWING
									+ ", "
									+ DingDattDatabase.FIELD_GALLERY_NAME
									+ ", "

									+ DingDattDatabase.FIELD_GALLERY_USER_ID
									+ ", "
									+ DingDattDatabase.FIELD_GALLERY_MY_USER_ID
									+ ""

									+ ") VALUES " + "(" + "'"
									+ object.getString("contest_id") + "',"
									+ "'"
									+ object.getString("contestparticipantid")
									+ "'," + "'"
									+ object.getString("uploaddate") + "',"
									+ "'" + object.getString("uploadfile")
									+ "'," + "'"
									+ object.getString("uploadtopic") + "',"

									+ "'" + object.getString("profilepicture")
									+ "'," + "'"
									+ object.getString("following") + "',"
									+ "'" + object.getString("name") + "',"

									+ "'" + object.getString("user_id") + "', "
									+ "'" + data.GetS("userid") + "'" + ")";

							Log.d(query, query);

							database.execSQL(query);
						}
					}
				} else {

					String msgcode = jsonObject.getJSONObject("response")
							.getString("msgcode");

					Toast.makeText(context,
							Main.getStringResourceByName(context, msgcode),
							Toast.LENGTH_LONG).show();
				}

				database.close();
			} else {

				Toast.makeText(context,
						Main.getStringResourceByName(context, "c100"),
						Toast.LENGTH_LONG).show();
			}

		} catch (Exception exp) {

			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	public static final int MEDIA_TYPE_VIDEO = 2;

	/**
	 * 
	 * @param type
	 * @return
	 */
	public static Uri getOutputMediaFileUri(int type) {

		return Uri.fromFile(getOutputMediaFile(type));
	}

	/** Create a File for saving an image or video */
	private static File getOutputMediaFile(int type) {

		File mediaStorageDir = new File(
				Environment
						.getExternalStoragePublicDirectory(Environment.DIRECTORY_PICTURES),
				"MyCameraVideo");

		if (!mediaStorageDir.exists()) {

			if (!mediaStorageDir.mkdirs()) {

				Toast.makeText(context,
						"Failed to create directory MyCameraVideo.",
						Toast.LENGTH_LONG).show();

				Log.d("MyCameraVideo",
						"Failed to create directory MyCameraVideo.");
				return null;
			}
		}

		java.util.Date date = new java.util.Date();
		String timeStamp = new SimpleDateFormat("yyyyMMdd_HHmmss").format(date
				.getTime());

		File mediaFile;

		if (type == MEDIA_TYPE_VIDEO) {

			mediaFile = new File(mediaStorageDir.getPath() + File.separator
					+ "VID_" + timeStamp + ".mp4");

		} else {
			return null;
		}

		return mediaFile;
	}

	private Uri fileUri;
	private static final int CAPTURE_VIDEO_ACTIVITY_REQUEST_CODE = 200;

	/**
	 * Open camera to get video
	 */
	private void openVideo() {
		Intent intent = new Intent(MediaStore.ACTION_VIDEO_CAPTURE);
		fileUri = getOutputMediaFileUri(MEDIA_TYPE_VIDEO);

		file_name = fileUri.getPath();
		intent.putExtra(MediaStore.EXTRA_OUTPUT, fileUri);
		intent.putExtra(MediaStore.EXTRA_VIDEO_QUALITY, 1);
		startActivityForResult(intent, CAPTURE_VIDEO_ACTIVITY_REQUEST_CODE);
	}

	private static int SHARE = 25;

	ProgressDialog pDialog;

	/**
	 * Post image into twitter.
	 * 
	 * @author karthik
	 *
	 */
	class updateTwitterStatus extends AsyncTask<String, String, Void> {
		@Override
		protected void onPreExecute() {
			super.onPreExecute();

			pDialog = new ProgressDialog(context);
			pDialog.setMessage("Posting to twitter...");
			pDialog.setIndeterminate(false);
			pDialog.setCancelable(false);
			pDialog.show();
		}

		protected Void doInBackground(String... args) {

			String status = args[0];
			try {
				System.out.println("test 1");
				ConfigurationBuilder builder = new ConfigurationBuilder();
				builder.setOAuthConsumerKey(consumerKey);
				builder.setOAuthConsumerSecret(consumerSecret);

				System.out.println("test 2");
				SharedPreferences sharedPreferences = context
						.getSharedPreferences(LocalData.MY_PREFS_NAME, 0);
				// Access Token
				String access_token = sharedPreferences.getString(
						PREF_KEY_OAUTH_TOKEN, "");
				// Access Token Secret
				String access_token_secret = sharedPreferences.getString(
						PREF_KEY_OAUTH_SECRET, "");

				System.out.println("test 3");
				AccessToken accessToken = new AccessToken(access_token,
						access_token_secret);
				Twitter twitter = new TwitterFactory(builder.build())
						.getInstance(accessToken);

				System.out.println("test 4");
				// Update status
				StatusUpdate statusUpdate = new StatusUpdate(status);

				System.out.println("test 5");
				Drawable d = show_image.getDrawable();
				BitmapDrawable bitDw = ((BitmapDrawable) d);
				Bitmap bitmap = bitDw.getBitmap();
				ByteArrayOutputStream stream = new ByteArrayOutputStream();
				bitmap.compress(Bitmap.CompressFormat.JPEG, 100, stream);
				byte[] imageInByte = stream.toByteArray();
				System.out.println("........length......" + imageInByte);
				ByteArrayInputStream bis = new ByteArrayInputStream(imageInByte);

				System.out.println("test 6");
				// InputStream is = getResources().openRawResource(
				// R.drawable.lakeside_view);
				statusUpdate.setMedia("test.jpg", bis);

				System.out.println("test 7");
				twitter4j.Status response = twitter.updateStatus(statusUpdate);

				System.out.println("test 8");
				Log.d("Status", response.getText());

			} catch (TwitterException e) {
				System.out.println("test 9 " + e.getMessage());
				Log.d("Failed to post!", e.getMessage());
			}
			return null;
		}

		@Override
		protected void onPostExecute(Void result) {

			/* Dismiss the progress dialog after sharing */
			pDialog.dismiss();

			Toast.makeText(context, "Posted to Twitter!", Toast.LENGTH_SHORT)
					.show();
		}

	}

	/**
	 * Initialize twitter configaration details
	 */
	private void initTwitterConfigs() {
		consumerKey = getString(R.string.twitter_consumer_key);
		consumerSecret = getString(R.string.twitter_consumer_secret);
		callbackUrl = getString(R.string.twitter_callback);
		oAuthVerifier = getString(R.string.twitter_oauth_verifier);
	}

	/**
	 * Login to twitter
	 */
	private void loginToTwitter() {

		SharedPreferences sharedPreferences = context.getSharedPreferences(
				LocalData.MY_PREFS_NAME, 0);
		boolean isLoggedIn = sharedPreferences.getBoolean(
				PREF_KEY_TWITTER_LOGIN, false);

		if (!isLoggedIn) {
			final ConfigurationBuilder builder = new ConfigurationBuilder();
			builder.setOAuthConsumerKey(consumerKey);
			builder.setOAuthConsumerSecret(consumerSecret);

			final Configuration configuration = builder.build();
			final TwitterFactory factory = new TwitterFactory(configuration);
			mTwitter = factory.getInstance();
			try {
				requestToken = mTwitter.getOAuthRequestToken(callbackUrl);
				final Intent intent = new Intent(context, WebviewActivity.class);
				intent.putExtra(WebviewActivity.EXTRA_URL,
						requestToken.getAuthenticationURL());
				startActivityForResult(intent, WEBVIEW_REQUEST_CODE);

			} catch (TwitterException e) {
				e.printStackTrace();
			}
		}
	}

	/**
	 * Get Participant Gallery Details form server
	 */
	private void GetParticipantGalleryDetails() {

		try {

			String url = StringURLs.PARTICIPANT_DETAILS;

			ArrayList<String> asNames = new ArrayList<String>();
			ArrayList<String> asValues = new ArrayList<String>();

			asNames.add("contest_id");
			asNames.add("timezone");
			asNames.add("participantuserid");

			asValues.add(Integer.toString(this.contest_id));
			asValues.add(Main.GetTimeZone());
			LocalData localData = new LocalData(context);

			asValues.add(localData.GetS("userid"));

			url = StringURLs.getQuery(url, asNames, asValues);

			ConnectServer connectServer = new ConnectServer();
			connectServer.setContext(context);
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {

					if (sJSON.length() > 0) {
						ShowParticipantGallery(sJSON);
					} else {
						Toast.makeText(context,
								Main.getStringResourceByName(context, "c100"),
								Toast.LENGTH_LONG).show();
					}
				}
			});

			connectServer.execute(url);
		} catch (Exception exp) {

			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
		}

	}

	/**
	 * Get user details who created this contest
	 */
	private void GetCreatedContestUserDetails() {

		try {

			String url = StringURLs.USER_PROFILE;

			ArrayList<String> asName = new ArrayList<String>();
			ArrayList<String> asValue = new ArrayList<String>();

			asName.add("userid");
			asName.add("timezone");

			asValue.add(contestdetails2.getCreatedby());
			asValue.add(Main.GetTimeZone());

			url = StringURLs.getQuery(url, asName, asValue);

			ConnectServer connectServer = new ConnectServer();
			connectServer.setContext(context);
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {

						if (sJSON.length() > 0) {

							Log.d("PROFILE JSON", "PROFILE JSON " + sJSON);

							JSONObject obj = new JSONObject(sJSON);
							JSONObject response = obj.getJSONObject("response");
							String success = response.getString("success");

							if (success.equalsIgnoreCase("1") == true) {

								JSONObject profile = obj
										.getJSONObject("profile");

								String profilepicture = profile
										.getString("profilepicture");
								String username = profile.getString("username");

								int loader = R.drawable.avator;
								ImageLoader imgLoader = new ImageLoader(context);
								if (profilepicture.length() == 0) {
									creatorimage.setImageResource(loader);
								} else {
									imgLoader.DisplayImage(profilepicture,
											loader, creatorimage);
								}

								creatorname.setText(username);
							} else {
								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();
							}

						} else {

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();
						}

					} catch (Exception exp) {

						Toast.makeText(context,
								Main.getStringResourceByName(context, "c100"),
								Toast.LENGTH_LONG).show();
					}
				}
			});

			connectServer.execute(url);

		} catch (Exception exp) {

			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * Shows Participated gallery into gallery tab
	 * 
	 * @param sJson
	 *            - JSON String
	 */
	private void ShowParticipantGallery(String sJson) {

		try {

			JSONObject jsonObject = new JSONObject(sJson);
			// JSONObject response = jsonObject.getJSONObject("response");
			JSONArray participantlist = jsonObject
					.getJSONArray("participantlist");

			for (int i = 0; i < participantlist.length(); i++) {
				JSONObject array = participantlist.getJSONObject(i);

				String imageloc = array.getString("uploadfile");

				String uploadtopic = array.getString("uploadtopic");

				if (array.getString("uploadfile").length() > 0) {
					this.view_img_loc = imageloc;
				}

				Log.d("Image loc", imageloc);

				if (contestType.equalsIgnoreCase(PHOTO)) {
					view_topic.setVisibility(View.GONE);
					view_gallery.setVisibility(View.VISIBLE);

					int loader = R.drawable.loader;
					ImageLoader imgLoader = new ImageLoader(context);
					imgLoader.DisplayImage(imageloc, loader, view_gallery);
				} else if (contestType.equalsIgnoreCase(VIDEO)) {

					view_topic.setVisibility(View.GONE);
					view_gallery.setVisibility(View.VISIBLE);

					Uri uri = Uri.fromFile(new File(imageloc));

					MediaMetadataRetriever mediaMetadataRetriever = new MediaMetadataRetriever();
					mediaMetadataRetriever.setDataSource(imageloc,
							new HashMap<String, String>());
					Bitmap bm = mediaMetadataRetriever.getFrameAtTime(100);

					view_gallery.setImageBitmap(bm);
				} else if (contestType.equalsIgnoreCase(TOPIC)) {

					view_topic.setVisibility(View.VISIBLE);
					view_gallery.setVisibility(View.GONE);

					view_topic.setText(uploadtopic);

				}
			}

		} catch (Exception exp) {

			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	private int GROUP = 0;
	private int FOLLOWER = 1;

	/**
	 * Get group and follower list from server
	 * 
	 * @param iType
	 *            -GROUP, FOLLOWER
	 */
	private void GetGroupFollowerList(int iType) {

		try {

			String sURL = "";

			if (iType == GROUP) {

				sURL = StringURLs.GROUP_CONTEST;
			} else if (iType == FOLLOWER) {

				sURL = StringURLs.FOLLOWER_CONTEST;
			}

			ArrayList<String> asName = new ArrayList<String>();
			ArrayList<String> asValue = new ArrayList<String>();

			asName.add("user_id");
			asName.add("timezone");
			asName.add("contest_id");

			LocalData data = new LocalData(context);
			asValue.add(data.GetS("userid"));
			asValue.add(Main.GetTimeZone());
			asValue.add(Integer.toString(contest_id));

			sURL = StringURLs.getQuery(sURL, asName, asValue);

			if (iType == GROUP) {

				ConnectServer connectServer = new ConnectServer();
				connectServer.setMode(ConnectServer.MODE_POST);
				connectServer.setContext(context);
				connectServer.setListener(new ConnectServerListener() {

					@Override
					public void onServerResponse(String sJSON,
							JSONObject jsonObject) {
						// TODO Auto-generated method stub

						if (sJSON.length() > 0) {

							mGroup = new ArrayList<GroupFollower>();
							UpdateGroupJSON(sJSON);

							InviteAdapter adapter = new InviteAdapter(context,
									mGroup, 0,
									new InviteAdapter.InviteLisener() {

										@Override
										public void onInviteListener(String sPos) {
											// TODO Auto-generated method stub
											String tag = listview.getTag()
													.toString();

											if (tag.equalsIgnoreCase("0") == true) {

												if (mGroup.get(
														Integer.parseInt(sPos))
														.getInvite() == 0
														|| mGroup
																.get(Integer
																		.parseInt(sPos))
																.getInvite() == -1) {
													String[] alist = { (mGroup
															.get(Integer
																	.parseInt(sPos))
															.getGroupid() + "") };

													InviteUninviteGroup(alist,
															INVITE);
												} else if (mGroup.get(
														Integer.parseInt(sPos))
														.getInvite() > 0) {
													String[] alist = { (mGroup
															.get(Integer
																	.parseInt(sPos))
															.getGroupid() + "") };

													InviteUninviteGroup(alist,
															UNINVITE);
												}
											} else if (tag
													.equalsIgnoreCase("1") == true) {

												if (mFollower.get(
														Integer.parseInt(sPos))
														.getInvite() == 0) {
													String[] alist = { (mFollower
															.get(Integer
																	.parseInt(sPos))
															.getFollowerid() + "") };

													InviteUninviteFollower(
															alist, INVITE);
												} else if (mFollower.get(
														Integer.parseInt(sPos))
														.getInvite() > 0) {
													String[] alist = { (mFollower
															.get(Integer
																	.parseInt(sPos))
															.getFollowerid() + "") };

													InviteUninviteFollower(
															alist, UNINVITE);
												}
											}
										}
									});

							listview.setAdapter(adapter);
							listview.setTag("0");

							if (mGroup.size() > 0)
								invite_all.setVisibility(View.VISIBLE);
							else
								invite_all.setVisibility(View.GONE);

						} else {

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();
						}
					}
				});

				connectServer.execute(sURL);
			} else {

				ConnectServer connectServer = new ConnectServer();
				connectServer.setMode(ConnectServer.MODE_POST);
				connectServer.setContext(context);
				connectServer.setListener(new ConnectServerListener() {

					@Override
					public void onServerResponse(String sJSON,
							JSONObject jsonObject) {
						// TODO Auto-generated method stub

						if (sJSON.length() > 0) {

							mFollower = new ArrayList<GroupFollower>();

							UpdateFollowerJSON(sJSON);

							InviteAdapter adapter = new InviteAdapter(context,
									mFollower, 1,
									new InviteAdapter.InviteLisener() {

										@Override
										public void onInviteListener(String sPos) {
											// TODO Auto-generated method stub
											String tag = listview.getTag()
													.toString();

											if (tag.equalsIgnoreCase("0") == true) {

												if (mGroup.get(
														Integer.parseInt(sPos))
														.getInvite() == 0
														|| mGroup
																.get(Integer
																		.parseInt(sPos))
																.getInvite() == -1) {
													String[] alist = { (mGroup
															.get(Integer
																	.parseInt(sPos))
															.getGroupid() + "") };

													InviteUninviteGroup(alist,
															INVITE);
												} else if (mGroup.get(
														Integer.parseInt(sPos))
														.getInvite() > 0) {
													String[] alist = { (mGroup
															.get(Integer
																	.parseInt(sPos))
															.getGroupid() + "") };

													InviteUninviteGroup(alist,
															UNINVITE);
												}
											} else if (tag
													.equalsIgnoreCase("1") == true) {

												if (mFollower.get(
														Integer.parseInt(sPos))
														.getInvite() == 0) {
													String[] alist = { (mFollower
															.get(Integer
																	.parseInt(sPos))
															.getFollowerid() + "") };

													InviteUninviteFollower(
															alist, INVITE);
												} else if (mFollower.get(
														Integer.parseInt(sPos))
														.getInvite() > 0) {
													String[] alist = { (mFollower
															.get(Integer
																	.parseInt(sPos))
															.getFollowerid() + "") };

													InviteUninviteFollower(
															alist, UNINVITE);
												}
											}
										}
									});

							listview.setAdapter(adapter);
							listview.setTag("1");

							if (mFollower.size() > 0)
								invite_all.setVisibility(View.VISIBLE);
							else
								invite_all.setVisibility(View.GONE);

						} else {

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();
						}
					}
				});

				connectServer.execute(sURL);
			}

		} catch (Exception exp) {

		}
	}

	int INVITE = 0;
	int UNINVITE = 1;

	/**
	 * This function is used to invite or uninvite selected groupid
	 * 
	 * @param groupid
	 *            - to invite the group
	 * @param invitetype
	 *            - INVITE, UNINVITE
	 */
	private void InviteUninviteGroup(String[] groupid, int invitetype) {

		try {
			String sURL = "";

			if (invitetype == INVITE) {

				sURL = StringURLs.INVITE_GROUP_CONTEST;
			} else if (invitetype == UNINVITE) {

				sURL = StringURLs.UNINVITE_GROUP_CONTEST;
			}

			ArrayList<String> asName = new ArrayList<String>();
			ArrayList<String> asValue = new ArrayList<String>();

			asName.add("invite_type");
			asName.add("contest_id");
			asName.add("group_id");
			asName.add("timezone");
			asName.add("user_id");

			String groups = "";

			if (groupid != null) {
				for (int j = 0; j < groupid.length; j++) {

					if (j == 0) {
						groups = groupid[j];
					} else {
						groups = groups + "," + groupid[j];
					}

				}
			}

			asValue.add("single");
			asValue.add(Integer.toString(contest_id));
			asValue.add(groups);
			asValue.add(Main.GetTimeZone());

			LocalData data = new LocalData(context);
			asValue.add(data.GetS("userid"));

			sURL = StringURLs.getQuery(sURL, asName, asValue);

			ConnectServer connectServer = new ConnectServer();
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setContext(context);
			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {

						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);
							JSONObject response = jsonObject2
									.getJSONObject("response");

							String success = response.getString("success");
							String message = response.getString("message");

							if (success.equalsIgnoreCase("1") == true) {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();

							} else {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();
							}

							GetGroupFollowerList(GROUP);

						} else {

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();
						}

					} catch (Exception exp) {

						Toast.makeText(context,
								Main.getStringResourceByName(context, "c100"),
								Toast.LENGTH_LONG).show();
					}

				}
			});

			connectServer.execute(sURL);

		} catch (Exception exp) {

		}
	}

	/**
	 * This function is used to invite or uninvite selected followerid
	 * 
	 * @param followerid
	 *            - to invite the follower
	 * @param invitetype
	 *            - INVITE, UNINVITE
	 */
	private void InviteUninviteFollower(String[] followerid, int invitetype) {

		try {

			String sURL = "";

			if (invitetype == INVITE) {

				sURL = StringURLs.INVITE_FOLLOWER_CONTEST;

			} else if (invitetype == UNINVITE) {

				sURL = StringURLs.UNINVITE_FOLLOWER_CONTEST;
			}

			ArrayList<String> asName = new ArrayList<String>();
			ArrayList<String> asValue = new ArrayList<String>();

			asName.add("invite_type");
			asName.add("contest_id");
			asName.add("follower_id");
			asName.add("timezone");
			asName.add("user_id");

			String followers = "";

			if (followerid != null) {
				for (int j = 0; j < followerid.length; j++) {

					if (j == 0) {
						followers = followerid[j];
					} else {
						followers = followers + "," + followerid[j];
					}

				}
			}

			asValue.add("single");
			asValue.add(Integer.toString(contest_id));
			asValue.add(followers);
			asValue.add(Main.GetTimeZone());
			LocalData data = new LocalData(context);

			asValue.add(data.GetS("userid"));

			sURL = StringURLs.getQuery(sURL, asName, asValue);

			ConnectServer connectServer = new ConnectServer();
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setContext(context);
			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {

						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);
							JSONObject response = jsonObject2
									.getJSONObject("response");

							String success = response.getString("success");
							String message = response.getString("message");

							if (success.equalsIgnoreCase("1") == true) {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();

							} else {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();
							}

							GetGroupFollowerList(FOLLOWER);

						} else {

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();
						}

					} catch (Exception exp) {

						Toast.makeText(context,
								Main.getStringResourceByName(context, "c100"),
								Toast.LENGTH_LONG).show();
					}

				}
			});

			connectServer.execute(sURL);

		} catch (Exception exp) {

			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * Update group details into JSON string
	 * 
	 * @param sJson
	 *            - JSON String
	 */
	private void UpdateGroupJSON(String sJson) {

		try {

			JSONObject jsonObject = new JSONObject(sJson);
			JSONObject response = jsonObject.getJSONObject("response");

			String success = response.getString("success");

			if (success.equalsIgnoreCase("1") == true) {

				JSONArray grouplist = jsonObject.getJSONArray("grouplist");

				for (int i = 0; i < grouplist.length(); i++) {

					JSONObject grouplist1 = grouplist.getJSONObject(i);

					GroupFollower group = new GroupFollower();
					group.setCreatedby(grouplist1.getInt("createdby"));
					group.setInvite(grouplist1.getInt("invite"));
					group.setGroupid(grouplist1.getInt("groupid"));
					group.setGroupname(grouplist1.getString("groupname"));
					group.setGrouptype(grouplist1.getString("grouptype"));

					if (grouplist1.getString("firstname").length() == 0) {
						group.setOwner(grouplist1.getString("username"));
					} else {
						group.setOwner(grouplist1.getString("firstname") + " "
								+ grouplist1.getString("lastname"));
					}

					group.setGroupimage(grouplist1.getString("groupimage"));

					mGroup.add(group);
				}

			} else {

				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");

				Toast.makeText(context,
						Main.getStringResourceByName(context, msgcode),
						Toast.LENGTH_LONG).show();
			}
		} catch (Exception exp) {

			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * Update follower details into JSON string
	 * 
	 * @param sJson
	 *            - JSON String
	 */
	private void UpdateFollowerJSON(String sJson) {

		try {

			JSONObject jsonObject = new JSONObject(sJson);
			JSONObject response = jsonObject.getJSONObject("response");

			String success = response.getString("success");

			if (success.equalsIgnoreCase("1") == true) {

				JSONArray grouplist = jsonObject.getJSONArray("followerlist");

				for (int i = 0; i < grouplist.length(); i++) {

					JSONObject grouplist1 = grouplist.getJSONObject(i);

					GroupFollower follower = new GroupFollower();
					/*
					 * follower.setFollowerprimaryid(grouplist1
					 * .getInt("followerprimaryid"));
					 */
					follower.setFollowerid(grouplist1.getInt("followerid"));
					follower.setInvite(grouplist1.getInt("invite"));
					follower.setProfilepicture(grouplist1
							.getString("profilepicture"));
					follower.setFirstname(grouplist1.getString("name"));

					mFollower.add(follower);
				}

			} else {

				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");

				Toast.makeText(context,
						Main.getStringResourceByName(context, msgcode),
						Toast.LENGTH_LONG).show();
			}
		} catch (Exception exp) {

			Toast.makeText(context,
					Main.getStringResourceByName(context, "x100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * This is used for join to contest
	 * 
	 * @param itype
	 *            = 0 (photo and video) or 1 (topic)
	 */
	private void JoinContest(int itype) {

		try {

			ArrayList<String> asName = new ArrayList<String>();
			asName.add("contest_id");
			asName.add("user_id");
			asName.add("timezone");

			LocalData data = new LocalData(context);

			ArrayList<String> asValue = new ArrayList<String>();
			asValue.add(Integer.toString(contest_id));
			asValue.add(data.GetS("userid"));
			asValue.add(Main.GetTimeZone());

			if (itype == 1) {
				asName.add("uploadtopic");
				asValue.add(show_topic.getText().toString());
			}

			String sUrl = StringURLs.JOIN_CONTEST;

			ConnectServerImage connectServer = new ConnectServerImage();
			connectServer.setContext(context);
			connectServer.setNames(asName);
			connectServer.setValues(asValue);

			if (itype == 0) {
				connectServer.SendImage("uploadfile", file_name);
			}

			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub
					try {
						if (sJSON.length() == 0) {

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();

						} else {
							JSONObject jsonObject1 = new JSONObject(sJSON);

							JSONObject response = jsonObject1
									.getJSONObject("response");

							String success = response.getString("success");

							if (success.equalsIgnoreCase("1")) {

								String val = "";

								if (contestType.equalsIgnoreCase(PHOTO)) {
									val = "Photo";
								} else if (contestType.equalsIgnoreCase(VIDEO)) {
									val = "Video";
								}
								if (contestType.equalsIgnoreCase(TOPIC)) {
									val = "Topic";
								}

								String msgcode = jsonObject1.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();

								contest_description.setVisibility(View.VISIBLE);
								contest_join.setVisibility(View.GONE);
								// contest_view.setVisibility(View.GONE);
								image_submit.setVisibility(View.GONE);
								listview_layout.setVisibility(View.GONE);

								GetContestInfoDetails(Integer
										.toString(contest_id));

							} else {
								String msgcode = jsonObject1.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();
							}
						}

					} catch (Exception exp) {

						Toast.makeText(context,
								Main.getStringResourceByName(context, "c100"),
								Toast.LENGTH_LONG).show();
					}
				}
			});

			connectServer.execute(sUrl);

		} catch (Exception exp) {

			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * Get contest details.
	 * 
	 * @param sContID
	 */
	private void GetContestInfoDetails(String sContID) {

		ArrayList<String> asName = new ArrayList<String>();
		asName.add("contestid");
		asName.add("user_id");
		asName.add("timezone");

		ArrayList<String> asValue = new ArrayList<String>();
		asValue.add(sContID);

		LocalData data = new LocalData(context);
		asValue.add(data.GetS("userid"));

		asValue.add(Main.GetTimeZone());

		String sUrl = "";

		try {
			sUrl = StringURLs
					.getQuery(StringURLs.CONTEST_INFO, asName, asValue);
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		ConnectServer connectServer = new ConnectServer();
		connectServer.setMode(ConnectServer.MODE_POST);
		connectServer.setContext(context);
		connectServer.setListener(new ConnectServerListener() {

			@Override
			public void onServerResponse(String sJSON, JSONObject jsonObject) {
				// TODO Auto-generated method stub

				if (sJSON.length() == 0) {

					Toast.makeText(context,
							Main.getStringResourceByName(context, "c100"),
							Toast.LENGTH_LONG).show();
				} else {
					GetContestDetailsFromJSON(sJSON);
					ShowContestInfoDeatils();
				}
			}
		});

		connectServer.execute(sUrl);
	}

	/**
	 * Shows contest details into screen.
	 */
	private void ShowContestInfoDeatils() {

		try {
			DateFormat formatter = new SimpleDateFormat("dd-MM-yyyy hh:mm a");
			SimpleDateFormat newFormat = new SimpleDateFormat("dd-MMM-yyyy");
			SimpleDateFormat timeformat = new SimpleDateFormat("hh:mm a");

			Date date = (Date) formatter.parse(contestdetails2
					.getConteststartdate());
			String sContestStartDate = newFormat.format(date) + "\n"
					+ timeformat.format(date);

			Date date1 = (Date) formatter.parse(contestdetails2
					.getContestenddate());
			String sContestEndDate = newFormat.format(date1) + "\n"
					+ timeformat.format(date1);

			Date date2 = (Date) formatter.parse(contestdetails2
					.getVotingstartdate());
			String sVotingStartDate = newFormat.format(date2) + "\n"
					+ timeformat.format(date2);

			Date date3 = (Date) formatter.parse(contestdetails2
					.getVotingenddate());
			String sVotingEndDate = newFormat.format(date3) + "\n"
					+ timeformat.format(date3);

			contest_start_date.setText(sContestStartDate);
			contest_end_date.setText(sContestEndDate);
			voting_start_date.setText(sVotingStartDate);
			voting_end_date.setText(sVotingEndDate);
			prize.setText("Contest Prize:$ " + contestdetails2.getPrize());
			contes_name.setText(contestdetails2.getContest_name());

			if (contestdetails2.getSponsorname().length() > 0) {

				sponsorlayout.setVisibility(View.VISIBLE);

				sponsorname.setText(contestdetails2.getSponsorname());

				int loader = R.drawable.avator;
				String sponserphoto1 = contestdetails2.getSponsorphoto();

				ImageLoader imgLoader = new ImageLoader(context);
				imgLoader.DisplayImage(sponserphoto1, loader, sponsorphoto);

			}

			// sponsorphoto.setText(contestdetails2.getContest_name());
			description.setText(contestdetails2.getDescription());

			int loader = R.drawable.loader;
			String image_url = contestdetails2.getThemephoto();

			ImageLoader imgLoader = new ImageLoader(context);
			imgLoader.DisplayImage(image_url, loader, contest_image);
			GetCreatedContestUserDetails();

		} catch (Exception exp) {

		}
	}

	/**
	 * Get contest details from JSON string
	 * 
	 * @param sJSON
	 *            - JSON string
	 */
	private void GetContestDetailsFromJSON(String sJSON) {

		try {

			JSONObject jsonObject = new JSONObject(sJSON);

			JSONObject response = jsonObject.getJSONObject("response");

			String success = response.getString("success");

			if (success.equalsIgnoreCase("1")) {

				JSONArray contestdetails = jsonObject
						.getJSONArray("contestdetails");

				for (int i = 0; i < contestdetails.length(); i++) {

					JSONObject contestdetailsobj = contestdetails
							.getJSONObject(i);
					contestdetails2 = new ContestDetails();

					contestdetails2.setID(contestdetailsobj.getInt("ID"));
					contestdetails2.setContest_name(contestdetailsobj
							.getString("contest_name"));
					contestdetails2.setDescription(contestdetailsobj
							.getString("description"));

					contestdetails2.setConteststartdate(contestdetailsobj
							.getString("conteststartdate"));
					contestdetails2.setContestenddate(contestdetailsobj
							.getString("contestenddate"));

					contestdetails2.setVotingstartdate(contestdetailsobj
							.getString("votingstartdate"));
					contestdetails2.setVotingenddate(contestdetailsobj
							.getString("votingenddate"));

					contestdetails2.setCreatedby(contestdetailsobj
							.getString("createdby"));

					contestdetails2.setSponsorname(contestdetailsobj
							.getString("sponsorname"));

					contestdetails2.setSponsorphoto(contestdetailsobj
							.getString("sponsorphoto"));

					contestdetails2.setContesttype(contestdetailsobj
							.getString("contesttype"));
					contestdetails2.setPrize(contestdetailsobj
							.getString("prize"));
					contestdetails2.setThemephoto(contestdetailsobj
							.getString("themephoto"));

					System.out.print("participant "
							+ contestdetailsobj.getInt("contestparticipantid"));

					if (contestdetailsobj.getInt("contestparticipantid") == 1)
						contestdetails2.setContestparticipantid(true);
					else
						contestdetails2.setContestparticipantid(false);
				}

				ShowCorrespondingMenuTab(contestdetails2);

			} else {

				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");

				Toast.makeText(context,
						Main.getStringResourceByName(context, msgcode),
						Toast.LENGTH_LONG).show();
			}
		} catch (Exception exp) {

			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * Show Corresponding menu tab like-join, invite, share, vote, gallery and
	 * leaderboard
	 * 
	 * @param contestDetails
	 *            - ContestDetails class
	 */
	private void ShowCorrespondingMenuTab(ContestDetails contestDetails) {

		try {
			ContestDetails data = contestDetails;
			SimpleDateFormat formatter = new SimpleDateFormat(
					"dd-MM-yyyy hh:mm a"); // yyyy-MM-dd HH:mm

			// join, invite, share, vote, gallery,
			Date date = new Date();
			Date conteststartdate = formatter.parse(data.getConteststartdate());
			Date contestenddate = formatter.parse(data.getContestenddate());

			Date votingstartdate = formatter.parse(data.getVotingstartdate());
			Date votingenddate = formatter.parse(data.getVotingenddate());

			LocalData localData = new LocalData(context);

			// hide all
			join_layout.setVisibility(View.GONE);
			gallery_layout.setVisibility(View.GONE);
			leaderboard_layout.setVisibility(View.GONE);
			share_layout.setVisibility(View.VISIBLE);
			group_layout.setVisibility(View.GONE);
			follower_layout.setVisibility(View.GONE);
			invite_layout.setVisibility(View.GONE);
			vote_layout.setVisibility(View.GONE);
			view_gallery_layout.setVisibility(View.GONE);

			if (data.getCreatedby().equalsIgnoreCase(localData.GetS("userid")) == true) {
				// invite = true;
				invite_layout.setVisibility(View.VISIBLE);
			}

			if (data.getContestparticipantid() == true) {
				// gallery = true;
				gallery_layout.setVisibility(View.VISIBLE);
			}

			if (date.compareTo(contestenddate) > 0) {
				invite_layout.setVisibility(View.GONE);
				share_layout.setVisibility(View.GONE);
			}

			if (date.compareTo(conteststartdate) >= 0
					&& date.compareTo(contestenddate) <= 0) {
				// join = true;

				join_layout.setVisibility(View.VISIBLE);
			}

			if (date.compareTo(votingstartdate) > 0
					&& date.compareTo(votingenddate) < 0) {
				// vote = true;

				vote_layout.setVisibility(View.VISIBLE);
			}

			if (date.compareTo(votingenddate) > 0) {
				gallery_layout.setVisibility(View.VISIBLE);
				leaderboard_layout.setVisibility(View.VISIBLE);
			}

			contestType = contestdetails2.getContesttype();

			if (contestType.equalsIgnoreCase(PHOTO)) {
				take_snap.setImageResource(R.drawable.join_photo_upload_icons);
				title1.setText("Upload Contest Photo");
				title2.setText("Publish Photo");
				instant_share.setVisibility(View.VISIBLE);
			} else if (contestType.equalsIgnoreCase(VIDEO)) {
				take_snap.setImageResource(R.drawable.join_video_gallery_icons);
				title1.setText("Upload Contest Video");
				title2.setText("Publish Video");
				instant_share.setVisibility(View.GONE);
			} else if (contestType.equalsIgnoreCase(TOPIC)) {
				take_snap.setImageResource(R.drawable.join_topic_gallery_icons);
				title1.setText("Upload Contest Topic");
				title2.setText("Publish Topic");
				instant_share.setVisibility(View.GONE);
				choose_other.setText("Edit topic");
			}
		} catch (Exception exp) {

		}
	}

	/**
	 * Open camera intent
	 */
	public void openCamera() {
		Intent cameraIntent = new Intent(
				android.provider.MediaStore.ACTION_IMAGE_CAPTURE);
		startActivityForResult(cameraIntent, 0);
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		// TODO Auto-generated method stub
		super.onActivityResult(requestCode, resultCode, data);

		if (requestCode == 0 && resultCode == Activity.RESULT_OK) {

			Bitmap photo = (Bitmap) data.getExtras().get("data");

			show_image.setImageBitmap(photo);
			show_image.setVisibility(View.VISIBLE);
			show_image_layout.setVisibility(View.VISIBLE);

			Uri tempUri = getImageUri(context.getApplicationContext(), photo);

			// CALL THIS METHOD TO GET THE ACTUAL PATH
			file_name = getRealPathFromURI(tempUri);

			if (GetSizeofFile(file_name) < 2) {
				contest_description.setVisibility(View.GONE);
				contest_join.setVisibility(View.GONE);
				// contest_view.setVisibility(View.GONE);
				image_submit.setVisibility(View.VISIBLE);
			} else {
				Toast.makeText(context, "Photo size must be less than 2MB.",
						Toast.LENGTH_LONG).show();
			}

		} else if (requestCode == SELECT_IMAGE
				&& resultCode == Activity.RESULT_OK) {
			Uri selectedImage = data.getData();
			String[] filePathColumn = { MediaStore.Images.Media.DATA };

			show_image.setVisibility(View.VISIBLE);
			show_image_layout.setVisibility(View.VISIBLE);

			Cursor cursor = context.getContentResolver().query(selectedImage,
					filePathColumn, null, null, null);
			cursor.moveToFirst();

			int columnIndex = cursor.getColumnIndex(filePathColumn[0]);
			String picturePath = cursor.getString(columnIndex);
			cursor.close();

			if (GetSizeofFile(picturePath) < 5) {
				contest_description.setVisibility(View.GONE);
				contest_join.setVisibility(View.GONE);
				// contest_view.setVisibility(View.GONE);
				image_submit.setVisibility(View.VISIBLE);
				show_image
						.setImageBitmap(BitmapFactory.decodeFile(picturePath));
				show_image.setVisibility(View.VISIBLE);
				file_name = picturePath;
			} else {
				Toast.makeText(context, "Photo size must be less than 2MB.",
						Toast.LENGTH_LONG).show();
			}

		} else if (requestCode == SELECT_VIDEO
				&& resultCode == Activity.RESULT_OK) {
			Uri selectedImage = data.getData();
			String[] filePathColumn = { MediaStore.Images.Media.DATA };

			show_image.setVisibility(View.VISIBLE);
			show_image_layout.setVisibility(View.VISIBLE);

			Cursor cursor = context.getContentResolver().query(selectedImage,
					filePathColumn, null, null, null);
			cursor.moveToFirst();

			int columnIndex = cursor.getColumnIndex(filePathColumn[0]);
			String picturePath = cursor.getString(columnIndex);
			cursor.close();

			if (GetSizeofFile(picturePath) < 5) {
				Uri uri = Uri.fromFile(new File(picturePath));

				MediaMetadataRetriever mediaMetadataRetriever = new MediaMetadataRetriever();
				mediaMetadataRetriever.setDataSource(context, uri);
				Bitmap bm = mediaMetadataRetriever.getFrameAtTime(1000);

				show_image.setImageBitmap(bm);
				show_image.setVisibility(View.VISIBLE);
				contest_description.setVisibility(View.GONE);
				contest_join.setVisibility(View.GONE);
				// contest_view.setVisibility(View.GONE);
				image_submit.setVisibility(View.VISIBLE);

				file_name = picturePath;
			} else {
				Toast.makeText(context, "Video size must be less than 5MB.",
						Toast.LENGTH_LONG).show();
			}
		} else if (requestCode == WEBVIEW_REQUEST_CODE) {
			if (resultCode == Activity.RESULT_OK) {
				Log.d("1", "66");
				String verifier = data.getExtras().getString(oAuthVerifier);
				try {
					Log.d("1", "77" + verifier);
					AccessToken accessToken = mTwitter.getOAuthAccessToken(
							requestToken, verifier);

					long userID = accessToken.getUserId();
					Log.d("1", "88" + verifier);
					final User user = mTwitter.showUser(userID);
					String username = user.getName();

					saveTwitterInfo(accessToken);
				} catch (Exception e) {
					Log.e("Twitter Login Failed", e.getMessage());
				}
			}
		} else if (requestCode == SHARE && resultCode == Activity.RESULT_OK) {

			Toast.makeText(context, "Message Shared.", Toast.LENGTH_SHORT)
					.show();
		} else if (requestCode == CAPTURE_VIDEO_ACTIVITY_REQUEST_CODE) {

			if (resultCode == Activity.RESULT_OK) {

				show_image.setVisibility(View.VISIBLE);
				show_image_layout.setVisibility(View.VISIBLE);

				if (GetSizeofFile(fileUri.getPath()) < 5) {
					contest_description.setVisibility(View.GONE);
					contest_join.setVisibility(View.GONE);
					// contest_view.setVisibility(View.GONE);
					image_submit.setVisibility(View.VISIBLE);

					MediaMetadataRetriever mediaMetadataRetriever = new MediaMetadataRetriever();
					mediaMetadataRetriever.setDataSource(context, fileUri);
					Bitmap bm = mediaMetadataRetriever.getFrameAtTime(1000);

					show_image.setImageBitmap(bm);
					show_image.setVisibility(View.VISIBLE);
				} else {
					Toast.makeText(context,
							"Video size must be less than 5MB.",
							Toast.LENGTH_LONG).show();
				}

			} else if (resultCode == Activity.RESULT_CANCELED) {

				Toast.makeText(context, "User cancelled the video capture.",
						Toast.LENGTH_LONG).show();

			} else {

				Toast.makeText(context, "Video capture failed.",
						Toast.LENGTH_LONG).show();
			}
		} else if (resultCode == Activity.RESULT_OK && requestCode == 5) {
			contest_description.setVisibility(View.GONE);
			contest_join.setVisibility(View.GONE);
			// contest_view.setVisibility(View.GONE);
			image_submit.setVisibility(View.VISIBLE);

			show_image_layout.setVisibility(View.GONE);
			show_image.setVisibility(View.GONE);
			show_topic.setVisibility(View.VISIBLE);
			show_topic.setText(data.getStringExtra("topic"));
		} else if (requestCode == REQUEST_CODE_TUMBLR_LOGIN) {
			if (resultCode == TumblrLoginActivity.TUMBLR_LOGIN_RESULT_CODE_SUCCESS) {
				String tumblr_token = data
						.getStringExtra(TumblrLoginActivity.TUMBLR_EXTRA_TOKEN);
				String tumblr_token_secret = data
						.getStringExtra(TumblrLoginActivity.TUMBLR_EXTRA_TOKEN_SECRET);
				String blog_name = data.getStringExtra("blog_name");

				LocalData localData = new LocalData(context);
				localData.Update("tumblr_token", tumblr_token);
				localData.Update("tumblr_token_secret", tumblr_token_secret);
				localData.Update("blog_name", blog_name);
			} else if (resultCode == TumblrLoginActivity.TUMBLR_LOGIN_RESULT_CODE_FAILURE) {
				Log.d("Dingdatt", "Tumblr LOGIN FAIL");
			}
		} else if (requestCode == 20) {
			leaderboard_message.setVisibility(View.GONE);

			contest_description.setVisibility(View.GONE);
			contest_join.setVisibility(View.GONE);
			// contest_view.setVisibility(View.GONE);
			image_submit.setVisibility(View.GONE);
			listview_layout.setVisibility(View.VISIBLE);

			view_gallery_layout.setVisibility(View.VISIBLE);
			GetGroupFollowerList(GROUP);
			view_gallery_layout.setVisibility(View.GONE);
		}
	}

	/**
	 * Post images to tumblr
	 * 
	 * @author karthik
	 *
	 */
	class TumblrPost extends AsyncTask<String, String, String> {

		ProgressDialog dialog;

		@Override
		protected void onPreExecute() {
			// TODO Auto-generated method stub
			super.onPreExecute();

			dialog = ProgressDialog.show(context, "",
					context.getString(R.string.processing_please_wait));
		}

		@Override
		protected void onPostExecute(String result) {
			// TODO Auto-generated method stub
			super.onPostExecute(result);

			if (result.equalsIgnoreCase("") == true) {
				Toast.makeText(context, "Error.", Toast.LENGTH_LONG).show();
			} else {
				Toast.makeText(context, "Photo successfully posted to tumblr.",
						Toast.LENGTH_LONG).show();
			}

			dialog.dismiss();
		}

		@Override
		protected String doInBackground(String... params) {
			// TODO Auto-generated method stub
			try {
				JumblrClient client = new JumblrClient(params[0], params[1]);

				// Give it a token
				client.setToken(params[2], params[3]);

				File file = new File(params[4]);

				String type = "";

				if (contestType.equalsIgnoreCase("p") == true)
					type = "Photo";
				else if (contestType.equalsIgnoreCase("v") == true)
					type = "Video";
				else if (contestType.equalsIgnoreCase("t") == true)
					type = "Topic";

				String sURL = StringURLs.MAIN + "contest_info/" + contest_id;
				String name = "Contest Name:"
						+ contestdetails2.getContest_name();
				// String type = "Contest type:" + contestType;
				String handled = "Organised by:"
						+ creatorname.getText().toString();

				PhotoPost photoPost = new PhotoPost();
				photoPost.setBlogName(params[5]);
				photoPost.setData(file);
				photoPost.setPhoto(new Photo(file));
				photoPost.setCaption(sURL + "\n" + name + "\n"
						+ "Contest type:" + type + "\n" + handled);

				long long1 = client.postCreate(params[5], photoPost.detail());
				return Long.toString(long1);

			} catch (Exception exp) {
				System.out.println(exp.getMessage() + exp);
			}

			return "";
		}
	}

	/**
	 * Get size of given file
	 * 
	 * @param filename
	 * @return return MB (long)
	 */
	public static long GetSizeofFile(String filename) {

		File file = new File(filename);
		return (file.length() / 1024) / 1024; // Size in MB
	}

	/**
	 * 
	 * @param accessToken
	 */
	private void saveTwitterInfo(AccessToken accessToken) {

		long userID = accessToken.getUserId();

		User user;
		try {
			user = mTwitter.showUser(userID);

			String username = user.getName();

			/* Storing oAuth tokens to shared preferences */
			SharedPreferences sharedPreferences = context.getSharedPreferences(
					LocalData.MY_PREFS_NAME, 0);
			Editor e = sharedPreferences.edit();

			e.putString(PREF_KEY_OAUTH_TOKEN, accessToken.getToken());
			e.putString(PREF_KEY_OAUTH_SECRET, accessToken.getTokenSecret());
			e.putBoolean(PREF_KEY_TWITTER_LOGIN, true);
			e.putString(PREF_USER_NAME, username);
			e.commit();

		} catch (TwitterException e1) {
			e1.printStackTrace();
		}
	}

	/**
	 * Get Image URI
	 * 
	 * @param inContext
	 * @param inImage
	 * @return
	 */
	public static Uri getImageUri(Context inContext, Bitmap inImage) {
		ByteArrayOutputStream bytes = new ByteArrayOutputStream();
		inImage.compress(Bitmap.CompressFormat.JPEG, 100, bytes);
		String path = Images.Media.insertImage(inContext.getContentResolver(),
				inImage, "Title", null);
		return Uri.parse(path);
	}

	/**
	 * Get path from URI
	 * 
	 * @param uri
	 * @return
	 */
	public static String getRealPathFromURI(Uri uri) {
		Cursor cursor = context.getContentResolver().query(uri, null, null,
				null, null);
		cursor.moveToFirst();
		int idx = cursor.getColumnIndex(MediaStore.Images.ImageColumns.DATA);
		return cursor.getString(idx);
	}

	/**
	 * 
	 * @author karthik
	 *
	 */
	private enum PendingAction {
		NONE, POST_PHOTO, POST_STATUS_UPDATE
	}

	private PendingAction pendingAction = PendingAction.NONE;
	private boolean canPresentShareDialogWithPhotos;

	public void onClickPostPhoto() {
		performPublish(PendingAction.POST_PHOTO,
				canPresentShareDialogWithPhotos);
	}

	private boolean hasPublishPermission() {
		Session session = Session.getActiveSession();
		return session != null
				&& session.getPermissions().contains("publish_actions");
	}

	private static final String PERMISSION = "publish_actions";

	private void performPublish(PendingAction action, boolean allowNoSession) {
		Session session = Session.getActiveSession();
		if (session != null) {
			pendingAction = action;
			if (/* hasPublishPermission() */session
					.isPermissionGranted("publish_actions")) {
				// We can do the action right away.
				handlePendingAction();
				return;
			} else if (session.isOpened()) {
				// We need to get new permissions, then complete the action when
				// we get called back.
				// handlePendingAction();
				session.requestNewPublishPermissions(new Session.NewPermissionsRequest(
						this, PERMISSION));
				return;
			}
		}

		if (allowNoSession) {
			pendingAction = action;
			handlePendingAction();
		}
	}

	@SuppressWarnings("incomplete-switch")
	private void handlePendingAction() {
		PendingAction previouslyPendingAction = pendingAction;

		pendingAction = PendingAction.NONE;

		switch (previouslyPendingAction) {
		case POST_PHOTO:
			postPhoto();
			break;
		}
	}

	private interface GraphObjectWithId extends GraphObject {
		String getId();
	}

	private void showPublishResult(String message, GraphObject result,
			FacebookRequestError error) {
		String title = null;
		String alertMessage = null;
		if (error == null) {
			title = "Success";
			String id = result.cast(GraphObjectWithId.class).getId();
			alertMessage = "Successfully posted to facebook.";
		} else {
			title = "Error";
			alertMessage = error.getErrorMessage();
		}

		Toast.makeText(context, alertMessage, Toast.LENGTH_SHORT).show();
	}

	private void postPhoto() {

		show_image.buildDrawingCache();
		Bitmap image = show_image.getDrawingCache();

		if (hasPublishPermission()) {
			Request request = Request.newUploadPhotoRequest(
					Session.getActiveSession(), image, new Request.Callback() {
						@Override
						public void onCompleted(Response response) {
							showPublishResult("Post photo",
									response.getGraphObject(),
									response.getError());
						}
					});
			Bundle parameters = request.getParameters();

			String type = "";

			if (contestType.equalsIgnoreCase("p") == true)
				type = "Photo";
			else if (contestType.equalsIgnoreCase("v") == true)
				type = "Video";
			else if (contestType.equalsIgnoreCase("t") == true)
				type = "Topic";

			String sURL = StringURLs.MAIN + "contest_info/" + contest_id;
			String name = "Contest Name:" + contestdetails2.getContest_name();
			// String type = "Contest type:" + contestType;
			String handled = "Organised by:" + creatorname.getText().toString();

			parameters.putString("message", sURL + "\n" + name + "\n"
					+ "Contest type:" + type + "\n" + handled);
			// add more params here
			request.setParameters(parameters);
			request.executeAsync();
		} else {
			pendingAction = PendingAction.POST_PHOTO;
		}
	}
}