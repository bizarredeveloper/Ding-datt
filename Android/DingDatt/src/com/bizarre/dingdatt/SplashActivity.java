package com.bizarre.dingdatt;

import java.util.ArrayList;
import java.util.List;

import org.json.JSONObject;

import com.bizarre.dingdatt.database.DingDattDatabase;
import com.bizarre.dingdatt.service.BackSyncService;
import com.bizarre.dingdatt.service.NotificationService;
import com.bizarre.dingdatt.strings.CheckNwConn;
import com.bizarre.dingdatt.strings.CommonConfig;
import com.bizarre.dingdatt.strings.JSONStrings;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;
import com.google.android.gms.gcm.GoogleCloudMessaging;

import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.provider.Settings.Secure;
import android.util.Log;
import android.widget.Toast;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager.NameNotFoundException;

public class SplashActivity extends Activity {

	GoogleCloudMessaging gcm;
	String gcmId = "";
	String contest_id = "";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_splash);

		CreateDatabase();

		Intent intent = new Intent(this, BackSyncService.class);
		startService(intent);

		Intent intent1 = new Intent(this, NotificationService.class);
		startService(intent1);

		gcm = GoogleCloudMessaging.getInstance(this);
		gcmId = CheckGcmID();

		if (gcmId.length() == 0) {
			if (CheckNwConn.connect(this) == true) {
				new GetGcmID().execute();
			} else {
				Toast.makeText(this,
						getString(R.string.internet_connection_not_available),
						Toast.LENGTH_LONG).show();
				finish();
			}
		} else {
			OpenActivty();
		}
	}

	private void OpenActivty() {

		/*
		 * Bundle bundle = getIntent().getExtras();
		 * 
		 * if(bundle != null) { String type = bundle.get("type").toString();
		 * 
		 * if(type.equalsIgnoreCase("notification") == true) { LocalData data =
		 * new LocalData(this); contest_id = data.GetS("contest_id"); } }
		 */

		Intent intent = getIntent();

		if (intent != null) {
			String action = intent.getAction();
			Uri data = intent.getData();

			if (action == Intent.ACTION_VIEW) {
				if (data != null) {

					List<String> asValue = data.getPathSegments();
					ArrayList<String> asValues = new ArrayList<String>();

					for (int i = 0; i < asValue.size(); i++) {
						asValues.add(asValue.get(i).toLowerCase());
					}

					if (asValues.size() > 0) {
						int iPos = asValues.indexOf("contest_info");

						if (iPos != -1) {
							contest_id = asValues.get(iPos + 1);
						}
					}
				}
			}
		}

		LocalData data = new LocalData(this);

		if (data.GetS("login").equalsIgnoreCase("f") == true) {
			RegisterFacebookSignup();
		} else if (data.GetS("login").equalsIgnoreCase("g") == true) {
			RegisterGoogleSignup();
		} else if (data.GetS("login").equalsIgnoreCase("l") == true) {
			Login();
		} else {
			new Thread(new Runnable() {

				@Override
				public void run() {
					// TODO Auto-generated method stub
					try {
						Thread.sleep(3000);

						Intent intent = new Intent(SplashActivity.this,
								HomeActivity.class);
						intent.putExtra("type", "create_contest");
						startActivity(intent);
						finish();

					} catch (Exception exp) {

					}
				}
			}).start();
		}
	}

	private void simplethread() {
		new Thread(new Runnable() {

			@Override
			public void run() {
				// TODO Auto-generated method stub
				try {
					Thread.sleep(3000);

					Intent intent = new Intent(SplashActivity.this,
							HomeActivity.class);
					intent.putExtra("type", "create_contest");
					startActivity(intent);
					finish();

				} catch (Exception exp) {

				}
			}
		}).start();
	}

	/**
     * 
     */
	private void CreateDatabase() {

		try {
			DingDattDatabase database = new DingDattDatabase(this);
			database.getWritableDatabase();
			database.close();

		} catch (Exception exp) {
			Log.d("DINGDATT", "database error." + exp.getMessage());
		}
	}

	private void startThread() {
		new Thread(new Runnable() {

			@Override
			public void run() {
				// TODO Auto-generated method stub
				try {
					Thread.sleep(3000);

					Intent intent = new Intent(SplashActivity.this,
							ContestListSampleActivity.class);
					intent.putExtra("contest_id", contest_id);
					startActivity(intent);
					finish();

				} catch (Exception exp) {

				}
			}
		}).start();
	}

	private void Login() {
		ArrayList<String> asName = new ArrayList<String>();
		asName.add("username");
		asName.add("password");
		asName.add("device_type");
		asName.add("device_id");
		asName.add("gcm_id");
		asName.add("timezone");

		LocalData data = new LocalData(this);

		ArrayList<String> asValue = new ArrayList<String>();
		asValue.add(data.GetS("username"));
		asValue.add(data.GetS("password"));
		asValue.add("A");

		String android_id = Secure.getString(
				SplashActivity.this.getContentResolver(), Secure.ANDROID_ID);

		asValue.add(android_id);

		LocalData data1 = new LocalData(SplashActivity.this);
		asValue.add(data1.GetS("gcmId"));
		asValue.add(Main.GetTimeZone());

		String sUrl = "";

		try {
			sUrl = StringURLs.getQuery(StringURLs.LOGIN, asName, asValue);

			Log.d("LOGIN URL", "LOGIN URL " + sUrl);

		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		ConnectServer connectServer = new ConnectServer();
		connectServer.setMode(ConnectServer.MODE_POST);
		connectServer.setContext(SplashActivity.this);
		connectServer.setListener(new ConnectServerListener() {

			@Override
			public void onServerResponse(String sJSON, JSONObject jsonObject) {

				try {

					if (sJSON.length() == 0) {

						Toast.makeText(
								SplashActivity.this,
								Main.getStringResourceByName(
										SplashActivity.this, "c100"),
								Toast.LENGTH_LONG).show();

					} else {
						String sMsg = CheckLoginJSON(sJSON);

						if (sMsg.length() == 0) {

							GetNotificationCount();
						}
					}

				} catch (Exception exp) {

				}
			}
		});

		connectServer.execute(sUrl);
	}

	/**
	 * Check login details from json string
	 * 
	 * @param json
	 * @return
	 */
	private String CheckLoginJSON(String json) {
		String sMsg = "";

		try {
			JSONObject jsonObject = new JSONObject(json);

			JSONObject response = jsonObject
					.getJSONObject(JSONStrings.JSON_RESPONSE);

			String sSuccess = response.getString(JSONStrings.JSON_SUCCESS);

			if (sSuccess.equalsIgnoreCase("1")) {
				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");

				Toast.makeText(
						SplashActivity.this,
						Main.getStringResourceByName(SplashActivity.this,
								msgcode), Toast.LENGTH_LONG).show();

				return "";
			} else {
				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");

				Toast.makeText(
						SplashActivity.this,
						Main.getStringResourceByName(SplashActivity.this,
								msgcode), Toast.LENGTH_LONG).show();

				sMsg = response.getString(JSONStrings.JSON_MESSAGE);
				return sMsg;
			}

		} catch (Exception exp) {

			Toast.makeText(SplashActivity.this,
					Main.getStringResourceByName(SplashActivity.this, "c100"),
					Toast.LENGTH_LONG).show();

			return getString(R.string.error);
		}
	}

	/**
	 * Get notification count
	 */
	private void GetNotificationCount() {
		try {

			String url = StringURLs.NOTIFICATION_COUNT;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("user_id");
			LocalData data = new LocalData(SplashActivity.this);
			values.add(data.GetS("userid"));
			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(SplashActivity.this);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {
						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);

							LocalData data = new LocalData(SplashActivity.this);
							data.Update("notificationcount",
									jsonObject2.getInt("admincount")
											+ jsonObject2.getInt("membercount"));

							startThread();
						} else {

							Toast.makeText(
									SplashActivity.this,
									Main.getStringResourceByName(
											SplashActivity.this, "c100"),
									Toast.LENGTH_LONG).show();
						}
					} catch (Exception exp) {

						Toast.makeText(
								SplashActivity.this,
								Main.getStringResourceByName(
										SplashActivity.this, "c100"),
								Toast.LENGTH_LONG).show();
					}
				}
			});
			connectServerImage.setMode(ConnectServerImage.MODE_POST);
			connectServerImage.setNames(names);
			connectServerImage.setValues(values);
			connectServerImage.execute(url);

		} catch (Exception exp) {

			Toast.makeText(SplashActivity.this,
					Main.getStringResourceByName(SplashActivity.this, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * Register using facebook login
	 */
	private void RegisterFacebookSignup() {

		try {
			LocalData data = new LocalData(SplashActivity.this);

			ArrayList<String> asName = new ArrayList<String>();
			ArrayList<String> asValue = new ArrayList<String>();

			asName.add("email");
			asName.add("firstname");
			asName.add("gender");
			asName.add("id");
			asName.add("lastname");
			asName.add("name");
			asName.add("link");

			asName.add("device_type");
			asName.add("device_id");
			asName.add("gcm_id");
			asName.add("timezone");

			asValue.add(data.GetS(LocalData.EMAIL));
			asValue.add(data.GetS(LocalData.FIRST_NAME));
			asValue.add(data.GetS(LocalData.GENDER));
			asValue.add(data.GetS(LocalData.ID));
			asValue.add(data.GetS(LocalData.LAST_NAME));
			asValue.add(data.GetS(LocalData.NAME));
			asValue.add(data.GetS(LocalData.LINK));
			asValue.add("A");

			String android_id = Secure
					.getString(SplashActivity.this.getContentResolver(),
							Secure.ANDROID_ID);

			asValue.add(android_id);

			LocalData data1 = new LocalData(SplashActivity.this);
			asValue.add(data1.GetS("gcmId"));
			asValue.add(Main.GetTimeZone());

			String sURL = StringURLs.FACEBOOK_LOGIN;
			// getQuery(StringURLs.FACEBOOK_LOGIN, asName, asValue);

			ConnectServerParam connectServer = new ConnectServerParam();
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setContext(SplashActivity.this);
			connectServer.setParams(asName, asValue);
			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					Log.d("JSON DATA", sJSON);

					try {

						if (sJSON.length() != 0) {
							JSONObject object = new JSONObject(sJSON);
							JSONObject response = object
									.getJSONObject(JSONStrings.JSON_RESPONSE);
							String sResult = response
									.getString(JSONStrings.JSON_SUCCESS);

							if (sResult.equalsIgnoreCase("1") == true) {

								String user_id = response.getString("userid");

								LocalData data = new LocalData(
										SplashActivity.this);
								data.Update("userid", user_id);
								data.Update("name", response.getString("name"));

								GetNotificationCount();

							} else if (sResult.equalsIgnoreCase("0") == true) {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										SplashActivity.this,
										Main.getStringResourceByName(
												SplashActivity.this, msgcode),
										Toast.LENGTH_LONG).show();
							}
						} else {

							Toast.makeText(
									SplashActivity.this,
									Main.getStringResourceByName(
											SplashActivity.this, "c100"),
									Toast.LENGTH_LONG).show();
						}

					} catch (Exception exp) {

						Toast.makeText(
								SplashActivity.this,
								Main.getStringResourceByName(
										SplashActivity.this, "c100"),
								Toast.LENGTH_LONG).show();
					}

				}
			});

			connectServer.execute(sURL);

		} catch (Exception exp) {

			Toast.makeText(SplashActivity.this,
					Main.getStringResourceByName(SplashActivity.this, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * Register using google+ login
	 */
	private void RegisterGoogleSignup() {

		try {
			LocalData data = new LocalData(SplashActivity.this);

			ArrayList<String> asName = new ArrayList<String>();
			ArrayList<String> asValue = new ArrayList<String>();

			asName.add("email");
			asName.add("firstname");
			asName.add("gender");
			asName.add("id");
			asName.add("lastname");
			asName.add("name");
			// asName.add("link");

			asName.add("device_type");
			asName.add("device_id");
			asName.add("gcm_id");
			asName.add("timezone");

			asValue.add(data.GetS(LocalData.EMAIL));
			asValue.add(data.GetS(LocalData.FIRST_NAME));
			asValue.add(data.GetS(LocalData.GENDER));
			asValue.add(data.GetS(LocalData.ID));
			asValue.add(data.GetS(LocalData.LAST_NAME));
			asValue.add(data.GetS(LocalData.NAME));
			// asValue.add(data.GetS(LocalData.LINK));

			asValue.add("A");

			String android_id = Secure
					.getString(SplashActivity.this.getContentResolver(),
							Secure.ANDROID_ID);

			asValue.add(android_id);

			LocalData data1 = new LocalData(SplashActivity.this);
			asValue.add(data1.GetS("gcmId"));
			asValue.add(Main.GetTimeZone());
			String sURL = StringURLs.getQuery(StringURLs.GOOGLE_LOGIN, asName,
					asValue);

			ConnectServer connectServer = new ConnectServer();
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setContext(SplashActivity.this);
			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					Log.d("JSON DATA", sJSON);

					try {

						if (sJSON.length() != 0) {
							JSONObject object = new JSONObject(sJSON);
							JSONObject response = object
									.getJSONObject(JSONStrings.JSON_RESPONSE);
							String sResult = response
									.getString(JSONStrings.JSON_SUCCESS);

							if (sResult.equalsIgnoreCase("1") == true) {

								String user_id = response.getString("userid");

								LocalData data = new LocalData(
										SplashActivity.this);
								data.Update("userid", user_id);
								data.Update("name", response.getString("name"));

								GetNotificationCount();

							} else if (sResult.equalsIgnoreCase("0") == true) {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										SplashActivity.this,
										Main.getStringResourceByName(
												SplashActivity.this, msgcode),
										Toast.LENGTH_LONG).show();
							}
						} else {

							Toast.makeText(
									SplashActivity.this,
									Main.getStringResourceByName(
											SplashActivity.this, "c100"),
									Toast.LENGTH_LONG).show();
						}

					} catch (Exception exp) {

						Toast.makeText(
								SplashActivity.this,
								Main.getStringResourceByName(
										SplashActivity.this, "c100"),
								Toast.LENGTH_LONG).show();
					}
				}
			});

			connectServer.execute(sURL);

		} catch (Exception exp) {

			Toast.makeText(SplashActivity.this,
					Main.getStringResourceByName(SplashActivity.this, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * Check gcm id is valid
	 * 
	 * @return
	 */
	private String CheckGcmID() {

		LocalData data = new LocalData(this);
		try {

			if (data.GetS("gcmId").length() == 0) {

				return "";
			}

			int currentversion = getAppVersion(SplashActivity.this);

			if (currentversion != data.GetI("app_version")) {
				return "";
			}

		} catch (Exception exp) {
			return "";
		}

		return data.GetS("gcmId");
	}

	/**
	 * Get gcm id
	 * 
	 * @author karthik
	 *
	 */
	private class GetGcmID extends AsyncTask<String, String, String> {
		ProgressDialog dialog;

		@Override
		protected void onPreExecute() {
			// TODO Auto-generated method stub
			super.onPreExecute();
			dialog = ProgressDialog.show(SplashActivity.this, "",
					SplashActivity.this
							.getString(R.string.processing_please_wait));
		}

		@Override
		protected void onPostExecute(String result) {
			// TODO Auto-generated method stub
			super.onPostExecute(result);

			if (dialog != null)
				dialog.dismiss();

			OpenActivty();
		}

		@Override
		protected String doInBackground(String... params) {
			// TODO Auto-generated method stub

			try {
				if (gcm == null) {
					gcm = GoogleCloudMessaging.getInstance(SplashActivity.this);
				}
				gcmId = gcm.register(CommonConfig.GCM_SENDER_ID);

				Log.d("GetGcmData gcm id", gcmId + "");

				LocalData data = new LocalData(SplashActivity.this);
				data.Update("gcmId", gcmId);
				data.Update("app_version", getAppVersion(SplashActivity.this));
			} catch (Exception exp) {
				Log.d("GCM ERROR", "GCM ERROR " + exp.getMessage());
			}
			return "";
		}

	}

	private static int getAppVersion(Context context) {
		try {
			PackageInfo packageInfo = context.getPackageManager()
					.getPackageInfo(context.getPackageName(), 0);
			return packageInfo.versionCode;
		} catch (NameNotFoundException e) {
			// should never happen
			throw new RuntimeException("Could not get package name: " + e);
		}
	}
}
