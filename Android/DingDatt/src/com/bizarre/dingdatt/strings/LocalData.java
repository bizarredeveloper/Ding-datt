package com.bizarre.dingdatt.strings;

import android.app.Activity;
import android.content.Context;
import android.content.SharedPreferences;

/**
 * Add and Update shared preference data.
 * 
 * @author karthik
 *
 */
public class LocalData {
	Context context;

	public static String MY_PREFS_NAME = "DingDattMobileApp";
	public static String ID = "id";
	public static String FIRST_NAME = "first_name";
	public static String GIVEN_NAME = "given_name";
	public static String FAMILY_NAME = "family_name";
	public static String NAME = "name";
	public static String LINK = "link";
	public static String LAST_NAME = "last_name";
	public static String GENDER = "gender";
	public static String EMAIL = "email";
	public static String REMEMBER_USER_NAME = "RUN";
	public static String REMEMBER_PASSWORD = "RP";

	public LocalData(Context context) {
		this.context = context;
	}

	/**
	 * Update to shared preference
	 * 
	 * @param name
	 * @param value
	 */
	public void Update(String name, String value) {
		SharedPreferences.Editor editor = context.getSharedPreferences(
				MY_PREFS_NAME, Activity.MODE_PRIVATE).edit();
		editor.putString(name, value);
		editor.commit();
	}

	/**
	 * Update to shared preference
	 * 
	 * @param name
	 * @param value
	 */
	public void Update(String name, int value) {
		SharedPreferences.Editor editor = context.getSharedPreferences(
				MY_PREFS_NAME, Activity.MODE_PRIVATE).edit();
		editor.putInt(name, value);
		editor.commit();
	}

	/**
	 * Get shared preference data
	 * 
	 * @param value
	 * @return
	 */
	public String GetS(String value) {
		SharedPreferences prefs = context.getSharedPreferences(MY_PREFS_NAME,
				Activity.MODE_PRIVATE);
		return prefs.getString(value, "");
	}

	/**
	 * Get shared preference data
	 * 
	 * @param value
	 * @return
	 */
	public int GetI(String value) {
		SharedPreferences prefs = context.getSharedPreferences(MY_PREFS_NAME,
				Activity.MODE_PRIVATE);
		return prefs.getInt(value, -1);
	}

	/**
	 * Clear shared preference data
	 */
	public void clear() {
		SharedPreferences.Editor editor = context.getSharedPreferences(
				MY_PREFS_NAME, Activity.MODE_PRIVATE).edit();
		editor.clear();
		editor.commit();
	}
}
