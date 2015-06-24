/*
 * Copyright (C) 2013 The Android Open Source Project
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package com.bizarre.dingdatt.gcmservice;

import org.json.JSONException;
import org.json.JSONObject;

import com.bizarre.dingdatt.ContestInfoSampleActivity;
import com.bizarre.dingdatt.HomeActivity;
import com.bizarre.dingdatt.R;
import com.bizarre.dingdatt.SplashActivity;
import com.bizarre.dingdatt.strings.LocalData;
import com.google.android.gms.gcm.GoogleCloudMessaging;

import android.app.IntentService;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.os.Bundle;
import android.support.v4.app.NotificationCompat;
import android.util.Log;

/**
 * This {@code IntentService} does the actual handling of the GCM message.
 * {@code GcmBroadcastReceiver} (a {@code WakefulBroadcastReceiver}) holds a
 * partial wake lock for this service while the service does its work. When the
 * service is finished, it calls {@code completeWakefulIntent()} to release the
 * wake lock.
 */
public class GcmIntentService extends IntentService {
	public static final int NOTIFICATION_ID = 1;
	private NotificationManager mNotificationManager;
	NotificationCompat.Builder builder;

	public GcmIntentService() {
		super("GcmIntentService");
	}

	@Override
	protected void onHandleIntent(Intent intent) {
		Bundle extras = intent.getExtras();
		GoogleCloudMessaging gcm = GoogleCloudMessaging.getInstance(this);
		// The getMessageType() intent parameter must be the intent you received
		// in your BroadcastReceiver.
		String messageType = gcm.getMessageType(intent);

		if (messageType != null) {

			Log.d("Message", "Message " + messageType);

			if (!extras.isEmpty()) { // has effect of unparcelling Bundle
				/*
				 * Filter messages based on message type. Since it is likely
				 * that GCM will be extended in the future with new message
				 * types, just ignore any message types you're not interested
				 * in, or that you don't recognize.
				 */
				if (GoogleCloudMessaging.MESSAGE_TYPE_SEND_ERROR
						.equals(messageType)) {
					// sendNotification("Send error: " + extras.toString());
				} else if (GoogleCloudMessaging.MESSAGE_TYPE_DELETED
						.equals(messageType)) {
					// sendNotification("Deleted messages on server: " +
					// extras.toString());
					// If it's a regular GCM message, do some work.
				} else if (GoogleCloudMessaging.MESSAGE_TYPE_MESSAGE
						.equals(messageType)) {
					// This loop represents the service doing some work.
					String message = intent.getExtras().getString(
							"notification");

					if (message != null && message.length() > 0) {

						LocalData data = new LocalData(getApplicationContext());
						data.Update("push_message", message);

						System.out.println("push_message " + message);
						// notifies user

						try {
							JSONObject jsonObj = new JSONObject(message);
							JSONObject notification = jsonObj
									.getJSONObject("notification");

							if (notification != null) {
								String user_id = notification
										.getString("user_id");
								String title = notification.getString("title");
								String msg = notification.getString("message");
								String contest_id = notification
										.getString("contest_id");

								if (data.GetS("userid").equalsIgnoreCase(
										user_id) == true) {

									data.Update("contest_id", contest_id);
									data.Update("user_id", user_id);
									sendNotification(title, msg);
								}
							}

						} catch (JSONException e) {
							// TODO Auto-generated catch block
							e.printStackTrace();
						}
					}

					// Log.i(CommonUtilities.TAG, "Received: " +
					// extras.toString());
				}
			}
		}

		// Release the wake lock provided by the WakefulBroadcastReceiver.
		GcmBroadcastReceiver.completeWakefulIntent(intent);
	}

	private void sendNotification(String title, String message) {

		mNotificationManager = (NotificationManager) this
				.getSystemService(Context.NOTIFICATION_SERVICE);

		/*
		 * Intent intent = new Intent(this, SplashActivity.class);
		 * //intent.putExtra("contest_id",
		 * Integer.parseInt(data.GetS("contest_id")));
		 * 
		 * Bundle bundle = new Bundle(); bundle.putString("type",
		 * "notification"); intent.putExtra("notification", bundle);
		 * 
		 * intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP |
		 * Intent.FLAG_ACTIVITY_SINGLE_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
		 */
		PendingIntent contentIntent = PendingIntent.getActivity(this, 0,
				new Intent(), 0);

		NotificationCompat.Builder mBuilder = new NotificationCompat.Builder(
				this)
				.setSmallIcon(R.drawable.app_icon)
				.setContentTitle(title)
				.setStyle(
						new NotificationCompat.BigTextStyle().bigText(message))
				.setContentText(message);

		mBuilder.setContentIntent(contentIntent);
		mBuilder.setAutoCancel(true);
		mNotificationManager.notify(NOTIFICATION_ID, mBuilder.build());
	}
}
