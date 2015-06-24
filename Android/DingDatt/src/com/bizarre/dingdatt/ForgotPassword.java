package com.bizarre.dingdatt;

import java.util.ArrayList;

import org.json.JSONObject;

import com.bizarre.dingdatt.strings.StringURLs;

import android.app.Activity;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

public class ForgotPassword extends Activity {

	EditText txtUsername;
	Button btnForgot;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_forgot_password);

		txtUsername = (EditText) findViewById(R.id.txtUsername);
		btnForgot = (Button) findViewById(R.id.btnForgot);

		btnForgot.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (txtUsername.getText().length() > 0)
					ForgotPwd();
			}
		});
	}

	/**
	 * Call forgot password service.
	 */
	private void ForgotPwd() {
		try {

			String url = StringURLs.FORGOT_PASSWORD;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("username");
			values.add(txtUsername.getText().toString().trim());

			ConnectServerParam connectServerParam = new ConnectServerParam();
			connectServerParam.setContext(this);
			connectServerParam.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {
						JSONObject object = new JSONObject(sJSON);

						JSONObject response = object.getJSONObject("response");

						if (response.getString("success").equalsIgnoreCase("1") == true) {
							String msgcode = jsonObject.getJSONObject(
									"response").getString("msgcode");

							Toast.makeText(
									ForgotPassword.this,
									Main.getStringResourceByName(
											ForgotPassword.this, msgcode),
									Toast.LENGTH_LONG).show();
							ForgotPassword.this.finish();
						} else {
							String msgcode = jsonObject.getJSONObject(
									"response").getString("msgcode");

							Toast.makeText(
									ForgotPassword.this,
									Main.getStringResourceByName(
											ForgotPassword.this, msgcode),
									Toast.LENGTH_LONG).show();
						}

					} catch (Exception exp) {

						Toast.makeText(
								ForgotPassword.this,
								Main.getStringResourceByName(
										ForgotPassword.this, "c100"),
								Toast.LENGTH_LONG).show();
					}
				}
			});

			connectServerParam.setMode(ConnectServerParam.MODE_POST);
			connectServerParam.setParams(names, values);
			connectServerParam.execute(url);
		} catch (Exception exp) {

		}
	}
}
