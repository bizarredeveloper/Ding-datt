package com.bizarre.dingdatt.service;

import java.util.ArrayList;
import java.util.Timer;
import java.util.TimerTask;

import org.json.JSONArray;
import org.json.JSONObject;

import com.bizarre.dingdatt.ConnectServerImage;
import com.bizarre.dingdatt.ConnectServerListener;
import com.bizarre.dingdatt.Main;
import com.bizarre.dingdatt.R;
import com.bizarre.dingdatt.database.DingDattDatabase;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;

import android.app.Service;
import android.content.Intent;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.os.AsyncTask;
import android.os.IBinder;
import android.util.Log;
import android.widget.Toast;

public class NotificationService extends Service {

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

	Timer timer;
	TimerTask timerTask;

	public void startTimer() {
		// set a new Timer
		timer = new Timer();

		// initialize the TimerTask's job
		initializeTimerTask();

		// schedule the timer, after the first 5000ms the TimerTask will run
		// every 10000ms
		timer.schedule(timerTask, 1000, 3000);
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
				String url = StringURLs.NOTIFICATION_COUNT;

				ArrayList<String> names = new ArrayList<String>();
				ArrayList<String> values = new ArrayList<String>();

				names.add("user_id");
				LocalData data = new LocalData(NotificationService.this);
				values.add(data.GetS("userid"));

				ConnectServerImage connectServerImage = new ConnectServerImage();
				connectServerImage.setShowdialog(0);
				connectServerImage.setContext(NotificationService.this);
				connectServerImage.setListener(new ConnectServerListener() {

					@Override
					public void onServerResponse(String sJSON,
							JSONObject jsonObject) {
						// TODO Auto-generated method stub
						try {
							if (sJSON.length() > 0) {

								JSONObject jsonObject2 = new JSONObject(sJSON);

								LocalData data = new LocalData(
										NotificationService.this);
								data.Update(
										"notificationcount",
										jsonObject2.getInt("admincount")
												+ jsonObject2
														.getInt("membercount"));

							} else {

								Log.d("Error", Main.getStringResourceByName(
										NotificationService.this, "c100"));
							}
						} catch (Exception exp) {

							Log.d("Error", Main.getStringResourceByName(
									NotificationService.this, "c100"));
						}
					}
				});

				connectServerImage.setMode(ConnectServerImage.MODE_POST);
				connectServerImage.setNames(names);
				connectServerImage.setValues(values);
				connectServerImage.execute(url);
			}
		};
	}

}