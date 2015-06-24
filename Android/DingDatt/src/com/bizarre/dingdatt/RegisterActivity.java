package com.bizarre.dingdatt;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.TimeZone;

import org.json.JSONObject;

import com.bizarre.dingdatt.strings.JSONStrings;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;

import android.os.Bundle;
import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.support.v4.app.DialogFragment;
import android.support.v4.app.FragmentActivity;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

public class RegisterActivity extends FragmentActivity {

	ImageView checkbox1;
	TextView checkbox2;
	int iChecked = 0;
	EditText username;
	EditText email;
	EditText password;
	TextView dob1;
	ImageView dob2;
	Button signup;
	Context context;
	protected int year = 0;
	protected int month = -1;
	protected int day = 0;
	static final int DATE_PICKER_ID = 999;
	public static Activity register;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_register);
		context = this;
		register = this;
		Init();
	}

	private void Init() {

		checkbox1 = (ImageView) findViewById(R.id.checkbox1);
		checkbox2 = (TextView) findViewById(R.id.checkbox2);

		username = (EditText) findViewById(R.id.username);
		email = (EditText) findViewById(R.id.email);
		password = (EditText) findViewById(R.id.password);
		dob1 = (TextView) findViewById(R.id.dob1);
		dob2 = (ImageView) findViewById(R.id.dob2);
		signup = (Button) findViewById(R.id.signup);

		dob1.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				DialogFragment newFragment = new DatePickerFragment(
						new DatePickerFragment.DateSelectListener() {

							@Override
							public void onDateSelect(String sDate) {
								// TODO Auto-generated method stub

								dob1.setText(sDate);
							}
						});
				newFragment.show(
						RegisterActivity.this.getSupportFragmentManager(),
						"datePicker");
			}
		});

		dob2.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				DialogFragment newFragment = new DatePickerFragment(
						new DatePickerFragment.DateSelectListener() {

							@Override
							public void onDateSelect(String sDate) {
								// TODO Auto-generated method stub

								dob1.setText(sDate);
							}
						});
				newFragment.show(
						RegisterActivity.this.getSupportFragmentManager(),
						"datePicker");
			}
		});

		signup.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (validate() == true) {
					String sUrl = "";

					// firstname, lastname, username, email, password, mobile,
					// gender, dateofbirth, timezone
					// http://192.168.1.52/dingdatt/mobileuserregister?username=karthik1&email=karthik@bizarresoftware.in&password=karthik1&dateofbirth=29-12-1990&timezone=asia/calcutta

					ArrayList<String> asName = new ArrayList<String>();
					asName.add("username");
					asName.add("email");
					asName.add("password");
					asName.add("dateofbirth");
					asName.add("timezone");

					ArrayList<String> asValue = new ArrayList<String>();
					asValue.add(username.getText().toString());
					asValue.add(email.getText().toString());
					asValue.add(password.getText().toString());
					asValue.add(dob1.getText().toString());

					asValue.add(Main.GetTimeZone());

					try {
						sUrl = StringURLs.getQuery(StringURLs.REGISTRATION,
								asName, asValue);
					} catch (Exception e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}

					ConnectServer connectServer = new ConnectServer();
					connectServer.setMode(ConnectServer.MODE_POST);
					connectServer.setListener(new ConnectServerListener() {

						@Override
						public void onServerResponse(String sJSON,
								JSONObject jsonObject) {
							// TODO Auto-generated method stub

							if (sJSON.length() == 0) {

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												""), Toast.LENGTH_LONG).show();
							} else {
								String sMsg = CheckLoginJSON(sJSON);

								if (sMsg.length() == 0) {

									Intent intent = new Intent(context,
											LoginActivity.class);
									startActivity(intent);

									finish();
								}
							}
						}
					});

					connectServer.setContext(context);
					connectServer.execute(sUrl);
				}
			}
		});

		checkbox1.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				CheckBoxEvent();
			}
		});

		checkbox2.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				CheckBoxEvent();
			}
		});
	}

	@Override
	public void onBackPressed() {
		// TODO Auto-generated method stub
		super.onBackPressed();

		Intent intent = new Intent(RegisterActivity.this, HomeActivity.class);
		startActivity(intent);
	}

	/**
	 * Check login details
	 * 
	 * @param json
	 * @return
	 */
	private String CheckLoginJSON(String json) {
		String sMsg = "";
		Log.d("", json);
		try {
			JSONObject jsonObject = new JSONObject(json);

			JSONObject response = jsonObject
					.getJSONObject(JSONStrings.JSON_RESPONSE);

			String sSuccess = response.getString(JSONStrings.JSON_SUCCESS);

			if (sSuccess.equalsIgnoreCase("1")) {
				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");

				Toast.makeText(context,
						Main.getStringResourceByName(context, msgcode),
						Toast.LENGTH_LONG).show();
				return "";
			} else {
				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");

				Toast.makeText(context,
						Main.getStringResourceByName(context, msgcode),
						Toast.LENGTH_LONG).show();

				sMsg = response.getString(JSONStrings.JSON_MESSAGE);
				return sMsg;
			}

		} catch (Exception exp) {

			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
			return "Error";
		}
	}

	/**
	 * Validate fields
	 * 
	 * @return
	 */
	private boolean validate() {
		if (username.getText().toString().length() == 0) {

			Toast.makeText(context, "Enter valid User Name.",
					Toast.LENGTH_SHORT).show();
			username.requestFocus();
			return false;
		} else if (email.getText().toString().length() == 0) {

			Toast.makeText(context, "Enter valid Email.", Toast.LENGTH_SHORT)
					.show();
			email.requestFocus();
			return false;
		} else if (password.getText().toString().length() == 0) {

			Toast.makeText(context, "Enter valid Password.", Toast.LENGTH_SHORT)
					.show();
			password.requestFocus();
			return false;
		} else if (dob1.getText().toString().length() == 0) {

			Toast.makeText(context, "Enter valid Date of Birth.",
					Toast.LENGTH_SHORT).show();
			dob1.requestFocus();
			return false;
		} else if (iChecked == 0) {

			Toast.makeText(context, "Please accept Terms and Conditions.",
					Toast.LENGTH_SHORT).show();
			return false;
		}

		return true;
	}

	private void CheckBoxEvent() {

		if (iChecked == 0) {

			iChecked = 1;
			checkbox1.setBackgroundResource(R.drawable.checkbox_tick);
		} else {

			iChecked = 0;
			checkbox1.setBackgroundResource(R.drawable.checkbox);
		}
	}
}
