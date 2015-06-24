package com.bizarre.dingdatt.service;

import java.util.ArrayList;
import java.util.Timer;
import java.util.TimerTask;

import org.json.JSONArray;
import org.json.JSONObject;

import com.bizarre.dingdatt.ConnectServerImage;
import com.bizarre.dingdatt.ConnectServerListener;
import com.bizarre.dingdatt.Main;
import com.bizarre.dingdatt.database.DingDattDatabase;
import com.bizarre.dingdatt.strings.StringURLs;

import android.app.Service;
import android.content.Intent;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.os.AsyncTask;
import android.os.IBinder;
import android.util.Log;

public class BackSyncService extends Service {

	@Override
	public void onCreate() {
		// TODO Auto-generated method stub
		super.onCreate();
		startTimer();
	}

	@Override
	public void onDestroy() {
		// TODO Auto-generated method stub
		super.onDestroy();
		stoptimertask();
	}

	@Override
	public IBinder onBind(Intent intent) {

		return null;
	}

	/**
	 * 
	 * @author karthik
	 *
	 */
	class SyncAsync extends AsyncTask<String, String, String> {

		@Override
		protected String doInBackground(String... params) {

			try {
				SQLiteDatabase database = SQLiteDatabase.openDatabase(
						DingDattDatabase.DATABASE_NAME, null,
						SQLiteDatabase.OPEN_READWRITE);

				String query = "SELECT * FROM "
						+ DingDattDatabase.TABLE_GALLERY + " WHERE "
						+ DingDattDatabase.FIELD_GALLERY_UPLOADEDSTATUS
						+ " = '" + 0 + "' AND "
						+ DingDattDatabase.FIELD_GALLERY_VOTINGSTATUS
						+ " IN ('L', 'U', 'P')";

				Cursor cursor = database.rawQuery(query, null);

				Log.d("Service ", "Service table count " + cursor.getCount());

				if (cursor.getCount() > 0) {
					cursor.moveToFirst();
					JSONObject jsonObject = new JSONObject(); // main
					JSONArray voteArray = new JSONArray();

					int i = 0;
					String userid = "";

					do {

						JSONObject value = new JSONObject();
						userid = cursor
								.getString(cursor
										.getColumnIndex(DingDattDatabase.FIELD_GALLERY_MY_USER_ID));

						value.put("user_id", userid);
						value.put(
								"contest_participant_id",
								cursor.getString(cursor
										.getColumnIndex(DingDattDatabase.FIELD_GALLERY_ID)));
						value.put(
								"vote",
								cursor.getString(cursor
										.getColumnIndex(DingDattDatabase.FIELD_GALLERY_VOTINGSTATUS)));

						voteArray.put(i, value);
						i++;

					} while (cursor.moveToNext());

					jsonObject.put("vote", voteArray);

					cursor.close();
					database.close();

					ConnectServer(jsonObject.toString(), userid);

				} else {
					cursor.close();
					database.close();
				}

			} catch (Exception e) {
				// TODO: handle exception
			}

			return "";
		}

		/**
		 * Update voting details to server from database
		 * 
		 * @param json
		 * @param userid
		 */
		private void ConnectServer(String json, String userid) {
			try {

				String url = StringURLs.VOTING;

				ArrayList<String> names = new ArrayList<String>();
				names.add("user_id");
				names.add("votingdetails");

				ArrayList<String> values = new ArrayList<String>();
				values.add(userid);
				values.add(json);

				ConnectServerImage connectServerImage = new ConnectServerImage();
				connectServerImage.setShowdialog(0);
				connectServerImage.setListener(new ConnectServerListener() {

					@Override
					public void onServerResponse(String sJSON,
							JSONObject jsonObject) {
						// TODO Auto-generated method stub

						try {

							if (sJSON.length() > 0) {

								System.out.println(sJSON);

								JSONObject jsonObject2 = new JSONObject(sJSON);

								if (jsonObject2.getJSONObject("response")
										.getString("success")
										.equalsIgnoreCase("1") == true) {

									String myuserid = jsonObject2
											.getJSONObject("response")
											.getString("user_id");

									JSONArray savedcontest_participant_id = jsonObject2
											.getJSONArray("savedcontest_participant_id");
									String participantids = "";

									for (int i = 0; i < savedcontest_participant_id
											.length(); i++) {

										if (participantids.length() == 0) {

											participantids = "'"
													+ savedcontest_participant_id
															.getString(i) + "'";

										} else {

											participantids = participantids
													+ ", "
													+ "'"
													+ savedcontest_participant_id
															.getString(i) + "'";
										}
									}

									SQLiteDatabase database = SQLiteDatabase
											.openDatabase(
													DingDattDatabase.DATABASE_NAME,
													null,
													SQLiteDatabase.OPEN_READWRITE);

									String query = "UPDATE "
											+ DingDattDatabase.TABLE_GALLERY
											+ " SET "
											+ DingDattDatabase.FIELD_GALLERY_UPLOADEDSTATUS
											+ "= '"
											+ 1
											+ "' WHERE"
											+ " "
											+ DingDattDatabase.FIELD_GALLERY_ID
											+ " IN ("
											+ participantids
											+ ")"
											+ " AND "
											+ DingDattDatabase.FIELD_GALLERY_MY_USER_ID
											+ " = '" + myuserid + "'";

									database.execSQL(query);
									database.execSQL("DELETE FROM "
											+ DingDattDatabase.TABLE_GALLERY
											+ " WHERE "
											+ DingDattDatabase.FIELD_GALLERY_UPLOADEDSTATUS
											+ " = '" + 1 + "'");
									database.close();

									Log.d(Main.TAG, Main
											.getStringResourceByName(
													BackSyncService.this,
													jsonObject2.getJSONObject(
															"response")
															.getString(
																	"msgcode")));

								} else {

									Log.d(Main.TAG, Main
											.getStringResourceByName(
													BackSyncService.this,
													jsonObject2.getJSONObject(
															"response")
															.getString(
																	"msgcode")));

									// Toast.makeText(BackSyncService.this,
									// jsonObject2.getJSONObject("response").getString("message"),
									// Toast.LENGTH_SHORT).show();
								}

							} else {

								Log.d(Main.TAG, Main.getStringResourceByName(
										BackSyncService.this, "c100"));
							}

						} catch (Exception exp) {

							Log.d(Main.TAG, Main.getStringResourceByName(
									BackSyncService.this, "c100"));
						}
					}
				});

				connectServerImage.setMode(ConnectServerImage.MODE_POST);
				connectServerImage.setNames(names);
				connectServerImage.setValues(values);
				connectServerImage.execute(url);

			} catch (Exception exp) {

			}
		}
	}

	Timer timer;
	TimerTask timerTask;

	public void startTimer() {
		// set a new Timer
		timer = new Timer();

		// initialize the TimerTask's job
		initializeTimerTask();

		// schedule the timer, after the first 5000ms the TimerTask will run
		// every 10000ms
		timer.schedule(timerTask, 5000, 35000);
	}

	public void stoptimertask() {
		// stop the timer, if it's not already null
		if (timer != null) {
			timer.cancel();
			timer = null;
		}
	}

	public void initializeTimerTask() {

		timerTask = new TimerTask() {
			public void run() {
				new SyncAsync().execute();
			}
		};
	}

}