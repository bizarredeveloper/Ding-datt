package com.bizarre.dingdatt;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.TimeZone;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.pm.ActivityInfo;
import android.content.pm.PackageManager;
import android.content.pm.PackageManager.NameNotFoundException;
import android.graphics.Color;
import android.view.View;
import android.widget.TextView;

import com.bizarre.dingdatt.strings.CommonConfig;
import com.bizarre.dingdatt.strings.LocalData;
import com.facebook.Session;
import com.facebook.UiLifecycleHelper;
import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.AdView;

public class Main {

	public static String TAG = "com.bizarre.dingdatt";

	/**
	 * Get time zone from time settings like Asia/Calcutta
	 * 
	 * @return time zone
	 */
	public static String GetTimeZone() {

		Calendar cal = Calendar.getInstance();
		TimeZone timeZone = cal.getTimeZone();

		System.out.print(timeZone.getID());

		return timeZone.getID();
	}

	/**
	 * Set advertisement
	 * 
	 * @param context
	 */
	public static void SetAdvertisment(Activity context) {

		try {
			AdView adView = (AdView) context.findViewById(R.id.adView);

			AdRequest adRequest = new AdRequest.Builder().addTestDevice(
					"6422E6275D1E3C7E82AAF5C5CE06EA09").build();

			// Load ads into Banner Ads
			adView.loadAd(adRequest);

		} catch (Exception exp) {

		}
	}

	/**
	 * Set advertisement
	 * 
	 * @param context
	 */
	public static void SetAdvertisment(View context) {

		try {
			AdView adView = (AdView) context.findViewById(R.id.adView);

			AdRequest adRequest = new AdRequest.Builder()
			// .addTestDevice("6422E6275D1E3C7E82AAF5C5CE06EA09")
					.build();

			// Load ads into Banner Ads
			adView.loadAd(adRequest);

		} catch (Exception exp) {

		}
	}

	/**
	 * Get string resource name
	 * 
	 * @param context
	 * @param text
	 * @return
	 */
	public static String getStringResourceByName(Context context, String text) {

		try {
			String packageName = context.getPackageName();
			int resId = context.getResources().getIdentifier(text, "string",
					packageName);
			return context.getString(resId);

		} catch (Exception exp) {

			return "Error";
		}
	}

	/**
	 * Get activities
	 */
	public static ArrayList<Activity> GetActivities() {

		ArrayList<Activity> activities = new ArrayList<Activity>();

		activities.add(CommentActivity.comment);
		activities.add(ContestGalleryActivity.gallery);
		activities.add(ContestInfoSampleActivity.info);
		activities.add(ContestListSampleActivity.list);
		activities.add(ContestVotingActivity.voting);
		activities.add(CreateEditContestActivity.createcontest);
		activities.add(CreateEditGroupActivity.creategroup);
		activities.add(EditProfileActivity.editprofile);
		activities.add(GroupListActivity.grouplist);
		activities.add(MemberListActivity.memberlist);
		activities.add(MyContestActivity.mycontest);
		activities.add(MyProfileActivity.myprofile);
		activities.add(NotificationActivity.notification);
		activities.add(RegisterActivity.register);

		return activities;

	}

	/**
	 * To logout
	 */
	public static void Logout() {

		try {

			ArrayList<Activity> activities = GetActivities();

			for (int i = 0; i < activities.size(); i++) {

				if (activities.get(i) != null) {
					activities.get(i).finish();
				}
			}
		} catch (Exception e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
	}

	/**
	 * Update drawer list
	 */
	public static String[] UpdateDrawerlist(Activity context, String[] strings) {

		LocalData data = new LocalData(context);

		if (data.GetS(LocalData.NAME).length() != 0) {
			strings[0] = context.getString(R.string.welcome) + " "
					+ data.GetS(LocalData.NAME);

		} /*
		 * else if (data.GetS("email").length() != 0) { strings[0] =
		 * context.getString(R.string.welcome) + " " + data.GetS("email"); }
		 */else {
			strings[0] = context.getString(R.string.welcome);
		}

		int noti = 0;

		if (data.GetI("notificationcount") != -1) {
			noti = data.GetI("notificationcount");
		}

		strings[8] = strings[8] + " [" + noti + "] ";

		return strings;
	}

	/**
	 * 
	 * @param context
	 * @param position
	 */
	public static void MenuList(Activity context, int position) {

		try {

			if (position == 4) {

				Intent intent = new Intent(context,
						CreateEditContestActivity.class);
				context.startActivity(intent);
				context.finish();

			} else if (position == 5) {

				Intent intent = new Intent(context, MyContestActivity.class);
				context.startActivity(intent);
				context.finish();
			} else if (position == 9) {

				LocalData data = new LocalData(context);

				if (data.GetS("Me") != null && data.GetS("Me") == "1") {

				} else {
					data.clear();
				}

				Logout();

				Intent intent = new Intent(context, LoginActivity.class);
				context.startActivity(intent);

			} else if (position == 3) {

				Intent intent = new Intent(context,
						ContestListSampleActivity.class);
				context.startActivity(intent);
				context.finish();

			} else if (position == 1) {

				Intent intent = new Intent(context, MyProfileActivity.class);
				context.startActivity(intent);
				context.finish();
			} else if (position == 2) {

				Intent intent = new Intent(context, EditProfileActivity.class);
				context.startActivity(intent);
				context.finish();

			} else if (position == 6) {

				Intent intent = new Intent(context,
						CreateEditGroupActivity.class);
				context.startActivity(intent);
				context.finish();
			} else if (position == 7) {

				Intent intent = new Intent(context, GroupListActivity.class);
				context.startActivity(intent);
				context.finish();
			} else if (position == 8) {
				Intent intent = new Intent(context, NotificationActivity.class);
				context.startActivity(intent);
				context.finish();
			}

		} catch (Exception exp) {

		}
	}

	/**
	 * Set title
	 * 
	 * @param view
	 * @param title
	 */
	public static void SetTitle(View view, String title) {

		TextView titletext = (TextView) view.findViewById(R.id.title);
		titletext.setText(title);
	}

	/**
	 * Enable or disable view
	 * 
	 * @param val
	 * @param view
	 */
	public static void Enable(boolean val, View view) {
		view.setClickable(val);
		view.setEnabled(val);
		view.setFocusable(val);

		if (val == true) {
			view.setBackgroundColor(Color.rgb(255, 255, 255));
		} else {
			view.setBackgroundColor(Color.rgb(214, 211, 206));
		}
	}
}
