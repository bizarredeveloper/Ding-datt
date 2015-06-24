package com.bizarre.dingdatt;

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
import com.facebook.Response;
import com.facebook.Session;
import com.facebook.SessionState;
import com.facebook.UiLifecycleHelper;
import com.facebook.Request.GraphUserCallback;
import com.facebook.model.GraphUser;
import com.google.android.gms.common.AccountPicker;

import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.provider.Settings.Secure;
import android.accounts.AccountManager;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

public class LoginActivity extends Activity {

	TextView login;
	EditText username;
	EditText password;
	TextView facebook_login;
	TextView google;
	TextView forgot_pwd;
	int iChecked = 0;
	private String mEmail;
	private static final String SCOPE = "oauth2:https://www.googleapis.com/auth/userinfo.profile";

	static final int REQUEST_CODE_PICK_ACCOUNT = 1000;
	static final int REQUEST_CODE_RECOVER_FROM_AUTH_ERROR = 1001;
	static final int REQUEST_CODE_RECOVER_FROM_PLAY_SERVICES_ERROR = 1002;

	public static Activity logina;

	/**
	 * Facebook callback function
	 */
	private Session.StatusCallback statusCallback = new Session.StatusCallback() {

		@SuppressWarnings("deprecation")
		@Override
		public void call(Session session, SessionState state,
				Exception exception) {

			Request.executeMeRequestAsync(session, new GraphUserCallback() {

				@Override
				public void onCompleted(GraphUser user, Response response) {

					try {

						System.out.println("Facebook null");

						if (user != null) {

							System.out.println("Facebook login "
									+ user.getInnerJSONObject());
							JSONObject object = user.getInnerJSONObject();
							LocalData data = new LocalData(LoginActivity.this);
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

							if (CheckNwConn.connect(LoginActivity.this) == false) {

								Toast.makeText(LoginActivity.this,
										"Internet not available !",
										Toast.LENGTH_SHORT).show();
							} else {
								RegisterFacebookSignup();
							}
						}
					} catch (Exception exp) {

					}
				}
			});
		}
	};

	private UiLifecycleHelper uiHelper;

	ProgressDialog dialog;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_login);

		logina = this;
		uiHelper = new UiLifecycleHelper(this, null);
		uiHelper.onCreate(savedInstanceState);

		init();

		Bundle bundle = getIntent().getExtras();

		if (bundle != null) {
			String g = bundle.getString("g");

			if (g != null && g.equalsIgnoreCase("g") == true) {
				getUsername();
			}
		}

		LocalData data = new LocalData(this);

		if (data != null
				&& data.GetS(LocalData.REMEMBER_USER_NAME).length() != 0) {
			username.setText(data.GetS(LocalData.REMEMBER_USER_NAME));
			password.setText(data.GetS(LocalData.REMEMBER_PASSWORD));

		} else {

			username.setText("");
			password.setText("");
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

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {

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
			return;
		}

		uiHelper.onActivityResult(requestCode, resultCode, data);
	}

	/**
	 * on click facebook login button
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

	@Override
	public void onBackPressed() {

		super.onBackPressed();

		Intent intent = new Intent(LoginActivity.this, HomeActivity.class);
		startActivity(intent);
	}

	/**
	 * Initiate fields
	 */
	private void init() {
		login = (TextView) findViewById(R.id.login);
		// facebook = (TextView) findViewById(R.id.facebook);
		facebook_login = (TextView) findViewById(R.id.facebook);
		google = (TextView) findViewById(R.id.google);
		username = (EditText) findViewById(R.id.username);
		password = (EditText) findViewById(R.id.password);
		forgot_pwd = (TextView) findViewById(R.id.forgot);

		forgot_pwd.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				Intent intent = new Intent(LoginActivity.this,
						ForgotPassword.class);
				startActivity(intent);
			}
		});

		facebook_login.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

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

		login.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (validate() == true) {

					Login();
				}
			}
		});
	}

	/**
	 * Login service called when login button is clicked.
	 */
	private void Login() {
		ArrayList<String> asName = new ArrayList<String>();
		asName.add("username");
		asName.add("password");
		asName.add("device_type");
		asName.add("device_id");
		asName.add("gcm_id");
		asName.add("timezone");

		ArrayList<String> asValue = new ArrayList<String>();
		asValue.add(username.getText().toString());
		asValue.add(password.getText().toString());
		asValue.add("A");

		String android_id = Secure.getString(
				LoginActivity.this.getContentResolver(), Secure.ANDROID_ID);

		asValue.add(android_id);

		LocalData data = new LocalData(LoginActivity.this);
		asValue.add(data.GetS("gcmId"));
		asValue.add(Main.GetTimeZone());

		String sUrl = "";

		try {
			sUrl = StringURLs.getQuery(StringURLs.LOGIN, asName, asValue);
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		ConnectServer connectServer = new ConnectServer();
		connectServer.setMode(ConnectServer.MODE_POST);
		connectServer.setContext(LoginActivity.this);
		connectServer.setListener(new ConnectServerListener() {

			@Override
			public void onServerResponse(String sJSON, JSONObject jsonObject) {
				// TODO Auto-generated method stub

				// {"response":{"success":"1","message":"successfully Login","userid":77}}

				if (sJSON.length() == 0) {

					Toast.makeText(
							LoginActivity.this,
							Main.getStringResourceByName(LoginActivity.this,
									"c100"), Toast.LENGTH_LONG).show();

				} else {
					String sMsg = CheckLoginJSON(sJSON);

					if (sMsg.length() == 0) {

						if (iChecked == 1) {

							LocalData localData = new LocalData(
									LoginActivity.this);
							localData.Update(LocalData.REMEMBER_USER_NAME,
									username.getText().toString());
							localData.Update(LocalData.REMEMBER_PASSWORD,
									password.getText().toString());
						}

						GetNotificationCount();
					}
				}
			}
		});

		connectServer.execute(sUrl);
	}

	/**
	 * Get Notification Count.
	 */
	private void GetNotificationCount() {
		try {

			String url = StringURLs.NOTIFICATION_COUNT;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("user_id");
			LocalData data = new LocalData(LoginActivity.this);
			values.add(data.GetS("userid"));
			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(LoginActivity.this);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {
						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);

							LocalData data = new LocalData(LoginActivity.this);
							data.Update("notificationcount",
									jsonObject2.getInt("admincount")
											+ jsonObject2.getInt("membercount"));

							Intent intent = new Intent(LoginActivity.this,
									ContestListSampleActivity.class);
							startActivity(intent);
							finish();
						} else {

							Toast.makeText(
									LoginActivity.this,
									Main.getStringResourceByName(
											LoginActivity.this, "c100"),
									Toast.LENGTH_LONG).show();
						}
					} catch (Exception exp) {

						Toast.makeText(
								LoginActivity.this,
								Main.getStringResourceByName(
										LoginActivity.this, "c100"),
								Toast.LENGTH_LONG).show();
					}
				}
			});
			connectServerImage.setMode(ConnectServerImage.MODE_POST);
			connectServerImage.setNames(names);
			connectServerImage.setValues(values);
			connectServerImage.execute(url);

		} catch (Exception exp) {

			Toast.makeText(LoginActivity.this,
					Main.getStringResourceByName(LoginActivity.this, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	private void getUsername() {

		if (dialog == null) {
			dialog = ProgressDialog.show(LoginActivity.this, "",
					LoginActivity.this
							.getString(R.string.processing_please_wait));
		} else if (dialog.isShowing() == false) {
			dialog.cancel();
			dialog = ProgressDialog.show(LoginActivity.this, "",
					LoginActivity.this
							.getString(R.string.processing_please_wait));
		}

		if (mEmail == null) {
			pickUserAccount();
		} else {
			if (isDeviceOnline()) {
				getTask(LoginActivity.this, mEmail, SCOPE).execute();
			} else {
				Toast.makeText(this, "No network connection available",
						Toast.LENGTH_SHORT).show();

				if (dialog != null) {
					dialog.dismiss();
				}
			}
		}
	}

	/**
	 * Register using facebook login
	 */
	private void RegisterFacebookSignup() {

		try {
			LocalData data = new LocalData(LoginActivity.this);

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
					LoginActivity.this.getContentResolver(), Secure.ANDROID_ID);

			asValue.add(android_id);

			LocalData data1 = new LocalData(LoginActivity.this);
			asValue.add(data1.GetS("gcmId"));
			asValue.add(Main.GetTimeZone());

			String sURL = StringURLs.FACEBOOK_LOGIN;
			// getQuery(StringURLs.FACEBOOK_LOGIN, asName, asValue);

			ConnectServerParam connectServer = new ConnectServerParam();
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setContext(LoginActivity.this);
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
										LoginActivity.this);
								data.Update("userid", user_id);
								data.Update("name", response.getString("name"));

								GetNotificationCount();

							} else if (sResult.equalsIgnoreCase("0") == true) {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										LoginActivity.this,
										Main.getStringResourceByName(
												LoginActivity.this, msgcode),
										Toast.LENGTH_LONG).show();
							}
						} else {

							Toast.makeText(
									LoginActivity.this,
									Main.getStringResourceByName(
											LoginActivity.this, "c100"),
									Toast.LENGTH_LONG).show();
							Toast.makeText(LoginActivity.this,
									"Login problem !", Toast.LENGTH_SHORT)
									.show();
						}

					} catch (Exception exp) {

						Toast.makeText(
								LoginActivity.this,
								Main.getStringResourceByName(
										LoginActivity.this, "c100"),
								Toast.LENGTH_LONG).show();
					}

				}
			});

			connectServer.execute(sURL);

		} catch (Exception exp) {

		}
	}

	/**
	 * Register using google+ login
	 * 
	 * @param i
	 */
	private void RegisterGoogleSignup(int i) {

		try {
			LocalData data = new LocalData(LoginActivity.this);

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
					LoginActivity.this.getContentResolver(), Secure.ANDROID_ID);

			asValue.add(android_id);

			LocalData data1 = new LocalData(LoginActivity.this);
			asValue.add(data1.GetS("gcmId"));
			asValue.add(Main.GetTimeZone());

			String sURL = StringURLs.getQuery(StringURLs.GOOGLE_LOGIN, asName,
					asValue);

			ConnectServer connectServer = new ConnectServer();
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setContext(LoginActivity.this);
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
										LoginActivity.this);
								data.Update("userid", user_id);
								data.Update("name", response.getString("name"));

								GetNotificationCount();

							} else if (sResult.equalsIgnoreCase("0") == true) {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										LoginActivity.this,
										Main.getStringResourceByName(
												LoginActivity.this, msgcode),
										Toast.LENGTH_LONG).show();
							}
						} else {

							Toast.makeText(
									LoginActivity.this,
									Main.getStringResourceByName(
											LoginActivity.this, "c100"),
									Toast.LENGTH_LONG).show();
						}

					} catch (Exception exp) {

						Toast.makeText(
								LoginActivity.this,
								Main.getStringResourceByName(
										LoginActivity.this, "c100"),
								Toast.LENGTH_LONG).show();
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

	private AbstractGetNameTask getTask(Activity activity, String email,
			String scope) {
		return new GetNameInForeground(activity, email, scope,
				new AbstractGetNameTask.ReturnGooglePlusData() {

					@Override
					public void ReturnJsonData(String sJson) {
						// TODO Auto-generated method stub

						try {
							if (dialog == null) {
								dialog = ProgressDialog
										.show(LoginActivity.this,
												"",
												LoginActivity.this
														.getString(R.string.processing_please_wait));
							}

							if (sJson != null
									&& sJson.equalsIgnoreCase("Error") == true) {
								Intent intent = new Intent(LoginActivity.this,
										LoginActivity.class);
								intent.putExtra("g", "g");
								startActivity(intent);
								finish();
							} else if (sJson != null && sJson.length() > 0) {

								JSONObject object = new JSONObject(sJson);
								LocalData data = new LocalData(
										LoginActivity.this);
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

								if (CheckNwConn.connect(LoginActivity.this) == false) {

									Toast.makeText(LoginActivity.this,
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

	/** Checks whether the device currently has a network connection */
	private boolean isDeviceOnline() {
		ConnectivityManager connMgr = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
		NetworkInfo networkInfo = connMgr.getActiveNetworkInfo();
		if (networkInfo != null && networkInfo.isConnected()) {
			return true;
		}
		return false;
	}

	private void pickUserAccount() {
		String[] accountTypes = new String[] { "com.google" };
		Intent intent = AccountPicker.newChooseAccountIntent(null, null,
				accountTypes, false, null, null, null, null);
		startActivityForResult(intent, REQUEST_CODE_PICK_ACCOUNT);
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
				String sUserID = response.getString(JSONStrings.JSON_USERID);

				LocalData data = new LocalData(LoginActivity.this);
				data.Update("userid", sUserID);
				data.Update("login", "l");
				data.Update("username", username.getText().toString());
				data.Update("name", response.getString("name"));
				data.Update("password", password.getText().toString().trim());

				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");

				Toast.makeText(
						LoginActivity.this,
						Main.getStringResourceByName(LoginActivity.this,
								msgcode), Toast.LENGTH_LONG).show();

				return "";
			} else {
				String mail = "";
				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");

				if (msgcode.equalsIgnoreCase("c196") == true) {

					mail = " ("
							+ jsonObject.getJSONObject("response").getString(
									"mailid") + ")";
				}

				Toast.makeText(
						LoginActivity.this,
						Main.getStringResourceByName(LoginActivity.this,
								msgcode) + mail, Toast.LENGTH_LONG).show();

				sMsg = response.getString(JSONStrings.JSON_MESSAGE);
				return sMsg;
			}

		} catch (Exception exp) {

			Toast.makeText(LoginActivity.this,
					Main.getStringResourceByName(LoginActivity.this, "c100"),
					Toast.LENGTH_LONG).show();
			return "error";
		}
	}

	/**
	 * Validate fields.
	 * 
	 * @return
	 */
	private boolean validate() {
		if (username.getText().toString().length() <= 0) {
			Toast.makeText(LoginActivity.this, "Enter valid Email/Username.",
					Toast.LENGTH_SHORT).show();
			username.requestFocus();
			return false;
		} else if (password.getText().toString().length() <= 0) {
			Toast.makeText(LoginActivity.this,
					getString(R.string.password_required), Toast.LENGTH_SHORT)
					.show();
			password.requestFocus();
			return false;
		}

		return true;
	}
}
