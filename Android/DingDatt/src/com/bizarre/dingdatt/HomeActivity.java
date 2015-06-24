package com.bizarre.dingdatt;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.List;

import org.json.JSONObject;

import com.bizarre.dingdatt.googleplus.AbstractGetNameTask;
import com.bizarre.dingdatt.googleplus.GetNameInForeground;
import com.bizarre.dingdatt.strings.CheckNwConn;
import com.bizarre.dingdatt.strings.JSONStrings;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;
import com.facebook.Request;
import com.facebook.Request.GraphUserCallback;
import com.facebook.Response;
import com.facebook.Session;
import com.facebook.SessionState;
import com.facebook.UiLifecycleHelper;
import com.facebook.model.GraphUser;
import com.google.android.gms.auth.GooglePlayServicesAvailabilityException;
import com.google.android.gms.auth.UserRecoverableAuthException;
import com.google.android.gms.common.AccountPicker;
import com.google.android.gms.common.GooglePlayServicesUtil;

import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.provider.Settings.Secure;
import android.accounts.AccountManager;
import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.pm.Signature;
import android.content.pm.PackageManager.NameNotFoundException;
import android.util.Base64;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.TextView;
import android.widget.Toast;

/**
 * 
 * @author karthik
 *
 */
public class HomeActivity extends Activity {

	TextView email;
	TextView login;
	int EMAIL = 1;
	int LOGIN = 2;

	TextView facebook_login;
	TextView google;
	String mEmail;
	private static final String SCOPE = "oauth2:https://www.googleapis.com/auth/userinfo.profile";
	static final int REQUEST_CODE_PICK_ACCOUNT = 1000;
	static final int REQUEST_CODE_RECOVER_FROM_AUTH_ERROR = 1001;
	static final int REQUEST_CODE_RECOVER_FROM_PLAY_SERVICES_ERROR = 1002;

	public static Activity home;

	/**
	 * Facebook callback function.
	 */
	private Session.StatusCallback statusCallback = new Session.StatusCallback() {

		@SuppressWarnings("deprecation")
		@Override
		public void call(Session session, SessionState state,
				Exception exception) {
			// TODO Auto-generated method stub
			Request.executeMeRequestAsync(session, new GraphUserCallback() {

				@Override
				public void onCompleted(GraphUser user, Response response) {
					// TODO Auto-generated method stub

					try {
						if (user != null) {

							JSONObject object = user.getInnerJSONObject();
							LocalData data = new LocalData(HomeActivity.this);
							data.Update(LocalData.FIRST_NAME,
									object.getString(LocalData.FIRST_NAME));
							data.Update(LocalData.GENDER,
									object.getString(LocalData.GENDER));
							data.Update(LocalData.ID,
									object.getString(LocalData.ID));
							data.Update(LocalData.LAST_NAME,
									object.getString(LocalData.LAST_NAME));
							data.Update(LocalData.LINK,
									object.getString(LocalData.LINK));
							data.Update(LocalData.NAME,
									object.getString(LocalData.NAME));
							data.Update(LocalData.EMAIL,
									user.getProperty(LocalData.EMAIL)
											.toString());
							data.Update("login", "f");

							if (CheckNwConn.connect(HomeActivity.this) == false) {

								Toast.makeText(HomeActivity.this,
										"Internet not available !",
										Toast.LENGTH_SHORT).show();
							} else {
								RegisterFacebookSignup();
							}
						}

						if (dialog != null) {
							dialog.dismiss();
						}
					} catch (Exception exp) {

						if (dialog != null) {
							dialog.dismiss();
						}
					}
				}
			});
		}
	};

	ProgressDialog dialog;

	private UiLifecycleHelper uiHelper;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_home);

		home = this;

		uiHelper = new UiLifecycleHelper(this, null);
		uiHelper.onCreate(savedInstanceState);

		Bundle bundle = getIntent().getExtras();

		if (bundle != null) {
			String g = bundle.getString("g");

			if (g != null && g.equalsIgnoreCase("g") == true) {
				getUsername();
			}
		}

		Log.d("f1", "f1");

		try {
			Log.d("f1", "OPEN");

			PackageInfo info = getPackageManager().getPackageInfo(
					"com.bizarre.dingdatt", PackageManager.GET_SIGNATURES);
			for (Signature signature : info.signatures) {
				MessageDigest md = MessageDigest.getInstance("SHA");
				md.update(signature.toByteArray());
				Log.d("KeyHash:",
						"KeyHash "
								+ Base64.encodeToString(md.digest(),
										Base64.DEFAULT));
			}
		} catch (NameNotFoundException e) {
			Log.d("f1", "ERROR");
		} catch (NoSuchAlgorithmException e) {
			Log.d("f1", "ERROR 1");
		} catch (Exception exp) {
			Log.d("f1", "ERROR 2");
		}

		LocalData data1 = new LocalData(this);

		String id1 = data1.GetS(LocalData.ID);

		if (id1 != null && id1.length() > 0) {
			if (CheckSession() == true) {

				if (dialog == null) {
					dialog = ProgressDialog
							.show(HomeActivity.this, "", HomeActivity.this
									.getString(R.string.processing_please_wait));
				}
			} else {

				LocalData data = new LocalData(HomeActivity.this);
				String id = data.GetS(LocalData.ID);

				if (id != null && id.length() > 0) {

					if (dialog == null) {
						dialog = ProgressDialog
								.show(HomeActivity.this,
										"",
										HomeActivity.this
												.getString(R.string.processing_please_wait));
					}

					RegisterGoogleSignup(1);
				} else {

					String mail = data.GetS(LocalData.NAME);

					if (mail != null && mail.length() > 0) {

						if (dialog == null) {
							dialog = ProgressDialog
									.show(HomeActivity.this,
											"",
											HomeActivity.this
													.getString(R.string.processing_please_wait));
						}

						Login();
					}
				}
			}
		} else {
			Session session = Session.getActiveSession();
			session.close();
		}

		email = (TextView) findViewById(R.id.email);
		login = (TextView) findViewById(R.id.login);
		// facebook = (TextView) findViewById(R.id.fb_login_button);
		facebook_login = (TextView) findViewById(R.id.fb_login_button);
		google = (TextView) findViewById(R.id.google);

		facebook_login.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (dialog == null) {
					dialog = ProgressDialog.show(HomeActivity.this, "",
							HomeActivity.this
									.getString(R.string.processing_please_wait));
				}

				onClickFacebookLogin();
			}
		});

		google.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				getUsername();
			}
		});

		email.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				Intent intent = new Intent(HomeActivity.this,
						RegisterActivity.class);
				startActivityForResult(intent, EMAIL);
			}
		});

		login.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				Intent intent = new Intent(HomeActivity.this,
						LoginActivity.class);
				startActivityForResult(intent, EMAIL);
				finish();
			}
		});
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
			LocalData data = new LocalData(HomeActivity.this);
			values.add(data.GetS("userid"));
			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(HomeActivity.this);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {
						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);

							LocalData data = new LocalData(HomeActivity.this);
							data.Update("notificationcount",
									jsonObject2.getInt("admincount")
											+ jsonObject2.getInt("membercount"));

							Intent intent = new Intent(HomeActivity.this,
									ContestListSampleActivity.class);
							startActivity(intent);
							setResult(RESULT_OK);
							finish();
						} else {

							Toast.makeText(
									HomeActivity.this,
									Main.getStringResourceByName(
											HomeActivity.this, "c100"),
									Toast.LENGTH_LONG).show();
						}
					} catch (Exception exp) {

						Toast.makeText(
								HomeActivity.this,
								Main.getStringResourceByName(HomeActivity.this,
										"c100"), Toast.LENGTH_LONG).show();
					}
				}
			});
			connectServerImage.setMode(ConnectServerImage.MODE_POST);
			connectServerImage.setNames(names);
			connectServerImage.setValues(values);
			connectServerImage.execute(url);

		} catch (Exception exp) {

			Toast.makeText(HomeActivity.this,
					Main.getStringResourceByName(HomeActivity.this, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * Call login service when click login button.
	 */
	private void Login() {

		try {

			ArrayList<String> asName = new ArrayList<String>();
			asName.add("username");
			asName.add("password");

			ArrayList<String> asValue = new ArrayList<String>();
			LocalData localData = new LocalData(HomeActivity.this);

			asValue.add(localData.GetS("name"));
			asValue.add(localData.GetS("password"));

			String sUrl = "";

			try {
				sUrl = StringURLs.getQuery(StringURLs.LOGIN, asName, asValue);
			} catch (Exception e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}

			ConnectServer connectServer = new ConnectServer();
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setContext(HomeActivity.this);
			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					// {"response":{"success":"1","message":"successfully Login","userid":77}}

					if (sJSON.length() == 0) {

						Toast.makeText(
								HomeActivity.this,
								Main.getStringResourceByName(HomeActivity.this,
										"c100"), Toast.LENGTH_LONG).show();

					} else {
						String sMsg = CheckLoginJSON(sJSON);

						if (sMsg.length() == 0) {

							GetNotificationCount();
						}
					}
				}
			});

			connectServer.execute(sUrl);

		} catch (Exception exp) {

		}
	}

	/**
	 * Check login json details.
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
				String sUserID = response.getString(JSONStrings.JSON_USERID);

				LocalData data = new LocalData(HomeActivity.this);
				data.Update("userid", sUserID);

				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");

				Toast.makeText(
						HomeActivity.this,
						Main.getStringResourceByName(HomeActivity.this, msgcode),
						Toast.LENGTH_LONG).show();

				return "";
			} else {
				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");

				Toast.makeText(
						HomeActivity.this,
						Main.getStringResourceByName(HomeActivity.this, msgcode),
						Toast.LENGTH_LONG).show();

				sMsg = response.getString(JSONStrings.JSON_MESSAGE);

				return sMsg;
			}

		} catch (Exception exp) {

			Toast.makeText(HomeActivity.this,
					Main.getStringResourceByName(HomeActivity.this, "c100"),
					Toast.LENGTH_LONG).show();

			return "Error";
		}
	}

	/**
	 * Register using facebook login
	 */
	private void RegisterFacebookSignup() {

		try {
			LocalData data = new LocalData(HomeActivity.this);

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

			String android_id = Secure.getString(
					HomeActivity.this.getContentResolver(), Secure.ANDROID_ID);

			asValue.add(android_id);

			LocalData data1 = new LocalData(HomeActivity.this);
			asValue.add(data1.GetS("gcmId"));
			asValue.add(Main.GetTimeZone());

			String sURL = StringURLs.FACEBOOK_LOGIN;
			// getQuery(StringURLs.FACEBOOK_LOGIN, asName, asValue);

			ConnectServerParam connectServer = new ConnectServerParam();
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setContext(HomeActivity.this);
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
										HomeActivity.this);
								data.Update("userid", user_id);
								data.Update("name", response.getString("name"));

								GetNotificationCount();

							} else if (sResult.equalsIgnoreCase("0") == true) {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										HomeActivity.this,
										Main.getStringResourceByName(
												HomeActivity.this, msgcode),
										Toast.LENGTH_LONG).show();
							}
						} else {

							Toast.makeText(
									HomeActivity.this,
									Main.getStringResourceByName(
											HomeActivity.this, "c100"),
									Toast.LENGTH_LONG).show();
						}

					} catch (Exception exp) {

						Toast.makeText(
								HomeActivity.this,
								Main.getStringResourceByName(HomeActivity.this,
										"c100"), Toast.LENGTH_LONG).show();
					}

				}
			});

			connectServer.execute(sURL);

		} catch (Exception exp) {

			Toast.makeText(HomeActivity.this,
					Main.getStringResourceByName(HomeActivity.this, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * Register using google+ login
	 * 
	 * @param i
	 */
	private void RegisterGoogleSignup(int i) {

		try {
			LocalData data = new LocalData(HomeActivity.this);

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

			String android_id = Secure.getString(
					HomeActivity.this.getContentResolver(), Secure.ANDROID_ID);

			asValue.add(android_id);

			LocalData data1 = new LocalData(HomeActivity.this);
			asValue.add(data1.GetS("gcmId"));
			asValue.add(Main.GetTimeZone());

			String sURL = StringURLs.getQuery(StringURLs.GOOGLE_LOGIN, asName,
					asValue);

			ConnectServer connectServer = new ConnectServer();
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setContext(HomeActivity.this);
			connectServer.setShowdialog(i);
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
										HomeActivity.this);
								data.Update("userid", user_id);
								data.Update("name", response.getString("name"));

								GetNotificationCount();

							} else if (sResult.equalsIgnoreCase("0") == true) {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										HomeActivity.this,
										Main.getStringResourceByName(
												HomeActivity.this, msgcode),
										Toast.LENGTH_LONG).show();
							}
						} else {

							Toast.makeText(
									HomeActivity.this,
									Main.getStringResourceByName(
											HomeActivity.this, "c100"),
									Toast.LENGTH_LONG).show();
						}

					} catch (Exception exp) {

						Toast.makeText(
								HomeActivity.this,
								Main.getStringResourceByName(HomeActivity.this,
										"c100"), Toast.LENGTH_LONG).show();
					}

					if (dialog != null) {
						dialog.dismiss();
					}
				}
			});

			connectServer.execute(sURL);

		} catch (Exception exp) {

			Log.d("", "");

			if (dialog != null) {
				dialog.dismiss();
			}
		}
	}

	/**
	 * 
	 */
	private void getUsername() {
		if (dialog == null) {
			dialog = ProgressDialog.show(HomeActivity.this, "",
					HomeActivity.this
							.getString(R.string.processing_please_wait));
		} else if (dialog.isShowing() == false) {
			dialog.cancel();
			dialog = ProgressDialog.show(HomeActivity.this, "",
					HomeActivity.this
							.getString(R.string.processing_please_wait));
		}

		if (mEmail == null) {
			pickUserAccount();
		} else {
			if (isDeviceOnline()) {
				getTask(HomeActivity.this, mEmail, SCOPE).execute();
			} else {
				Toast.makeText(this, "No network connection available",
						Toast.LENGTH_SHORT).show();
				if (dialog != null) {
					dialog.dismiss();
				}
			}
		}
	}

	private AbstractGetNameTask getTask(Activity activity, String email,
			String scope) {

		return new GetNameInForeground(activity, email, scope,
				new AbstractGetNameTask.ReturnGooglePlusData() {

					@Override
					public void ReturnJsonData(String sJson) {
						// TODO Auto-generated method stub

						try {
							if (dialog == null) {
								dialog = ProgressDialog.show(
										HomeActivity.this,
										"",
										HomeActivity.this
												.getString(R.string.processing_please_wait));
							}

							if (sJson != null
									&& sJson.equalsIgnoreCase("Error") == true) {
								Intent intent = new Intent(HomeActivity.this,
										HomeActivity.class);
								intent.putExtra("g", "g");
								startActivity(intent);
								finish();
							} else if (sJson != null && sJson.length() > 0) {

								JSONObject object = new JSONObject(sJson);

								LocalData data = new LocalData(
										HomeActivity.this);
								data.Update(LocalData.FIRST_NAME,
										object.getString(LocalData.GIVEN_NAME));
								data.Update(LocalData.GENDER,
										object.getString(LocalData.GENDER));
								data.Update(LocalData.ID,
										object.getString(LocalData.ID));
								data.Update(LocalData.LAST_NAME,
										object.getString(LocalData.FAMILY_NAME));
								data.Update(LocalData.LINK,
										object.getString(LocalData.LINK));
								data.Update(LocalData.NAME,
										object.getString(LocalData.NAME));
								data.Update(LocalData.EMAIL, mEmail);
								data.Update("login", "g");

								if (CheckNwConn.connect(HomeActivity.this) == false) {

									Toast.makeText(HomeActivity.this,
											"Internet not available !",
											Toast.LENGTH_SHORT).show();

									if (dialog != null) {
										dialog.dismiss();
									}
								} else {
									RegisterGoogleSignup(0);
								}
							} else {

								if (dialog != null) {
									dialog.dismiss();
								}
							}
						} catch (Exception exp) {

							if (dialog != null) {
								dialog.dismiss();
							}
						}
					}
				});
	}

	public void handleException(final Exception e) {
		runOnUiThread(new Runnable() {
			@Override
			public void run() {
				if (e instanceof GooglePlayServicesAvailabilityException) {
					// The Google Play services APK is old, disabled, or not
					// present.
					// Show a dialog created by Google Play services that allows
					// the user to update the APK
					int statusCode = ((GooglePlayServicesAvailabilityException) e)
							.getConnectionStatusCode();
					Dialog dialog = GooglePlayServicesUtil.getErrorDialog(
							statusCode, HomeActivity.this,
							REQUEST_CODE_RECOVER_FROM_PLAY_SERVICES_ERROR);
					dialog.show();
				} else if (e instanceof UserRecoverableAuthException) {
					// Unable to authenticate, such as when the user has not yet
					// granted
					// the app access to the account, but the user can fix this.
					// Forward the user to an activity in Google Play services.
					Intent intent = ((UserRecoverableAuthException) e)
							.getIntent();
					startActivityForResult(intent,
							REQUEST_CODE_RECOVER_FROM_PLAY_SERVICES_ERROR);
				}
			}
		});
	}

	/** Checks whether the device currently has a network connection */
	private boolean isDeviceOnline() {
		ConnectivityManager connMgr = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
		NetworkInfo networkInfo = connMgr.getActiveNetworkInfo();
		if (networkInfo != null && networkInfo.isConnected()) {
			return true;
		}
		return false;
	}

	private void handleAuthorizeResult(int resultCode, Intent data) {
		if (data == null) {
			return;
		}
		if (resultCode == RESULT_OK) {
			getTask(this, mEmail, SCOPE).execute();
			return;
		}
		if (resultCode == RESULT_CANCELED) {
			return;
		}
	}

	/**
	 * On click facebook login button
	 */
	private void onClickFacebookLogin() {
		Session session = Session.getActiveSession();

		List<String> permissions = new ArrayList<String>();
		permissions.add("email");

		if (session.isOpened()) {
			session.close();
		} else {
			Session.openActiveSession(this, true, permissions, statusCallback);
		}
	}

	/**
	 * Check session is open or close
	 * 
	 * @return
	 */
	private boolean CheckSession() {

		Session session = Session.getActiveSession();

		if (session.isOpened()) {
			return true;
		} else {
			return false;
		}
	}

	@Override
	public void onResume() {
		super.onResume();

		uiHelper.onResume();
	}

	@Override
	public void onPause() {
		super.onPause();
		uiHelper.onPause();
	}

	@Override
	public void onDestroy() {
		super.onDestroy();

		if (dialog != null)
			dialog.dismiss();

		uiHelper.onDestroy();
	}

	@Override
	public void onSaveInstanceState(Bundle outState) {
		super.onSaveInstanceState(outState);
		uiHelper.onSaveInstanceState(outState);
	}

	private void pickUserAccount() {
		String[] accountTypes = new String[] { "com.google" };
		Intent intent = AccountPicker.newChooseAccountIntent(null, null,
				accountTypes, false, null, null, null, null);
		startActivityForResult(intent, REQUEST_CODE_PICK_ACCOUNT);
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		// TODO Auto-generated method stub
		super.onActivityResult(requestCode, resultCode, data);

		if (requestCode == REQUEST_CODE_PICK_ACCOUNT) {
			if (resultCode == RESULT_OK) {
				mEmail = data.getStringExtra(AccountManager.KEY_ACCOUNT_NAME);
				getUsername();
			} else if (resultCode == RESULT_CANCELED) {
				Toast.makeText(this, "You must pick an account",
						Toast.LENGTH_SHORT).show();

				if (dialog != null)
					dialog.dismiss();
			}
		} else if ((requestCode == REQUEST_CODE_RECOVER_FROM_AUTH_ERROR || requestCode == REQUEST_CODE_RECOVER_FROM_PLAY_SERVICES_ERROR)
				&& resultCode == RESULT_OK) {
			handleAuthorizeResult(resultCode, data);
			return;
		} else if (requestCode == EMAIL) {
			uiHelper.onDestroy();
			finish();
			return;
		}

		uiHelper.onActivityResult(requestCode, resultCode, data);
	}
}
