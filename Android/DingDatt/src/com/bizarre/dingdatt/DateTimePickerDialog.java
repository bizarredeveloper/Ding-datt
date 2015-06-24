package com.bizarre.dingdatt;

import java.text.SimpleDateFormat;
import java.util.Date;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.TimePicker;
import android.widget.Toast;
import android.widget.TimePicker.OnTimeChangedListener;

public class DateTimePickerDialog extends Activity {
	DatePicker datePicker;
	TimePicker timePicker;
	Button ok;
	Button cancel;
	int iHour;
	int iMin;
	int iSec;
	String predate = "";

	@SuppressWarnings("deprecation")
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		setContentView(R.layout.date_time_picker_dialog);
		Bundle bundle = getIntent().getExtras();

		datePicker = (DatePicker) findViewById(R.id.datePicker1);
		timePicker = (TimePicker) findViewById(R.id.timePicker1);
		ok = (Button) findViewById(R.id.ok);

		if (bundle != null) {
			String date = bundle.getString("date");
			predate = bundle.getString("predate");

			if (bundle.getBoolean("enabletime", true) == false) {
				timePicker.setVisibility(View.GONE);
			} else {
				datePicker.setMinDate(System.currentTimeMillis() - 1000);
			}

			if (date.length() != 0) {

				SimpleDateFormat formatter = new SimpleDateFormat(
						"dd-MM-yyyy hh:mm a"); // yyyy-MM-dd HH:mm

				try {

					if (predate != null && predate.length() > 0) {
						Date objpredate = formatter.parse(predate);
						datePicker.setMinDate(objpredate.getTime());
					}

					Date objDate = formatter.parse(date);

					datePicker.init(1900 + objDate.getYear(),
							objDate.getMonth(), objDate.getDate(), null);
					timePicker.setCurrentHour(objDate.getHours());
					timePicker.setCurrentMinute(objDate.getMinutes());

				} catch (Exception exp) {

					Log.d("", "");
				}
			}
		}

		getHoursMin();
	}

	/**
	 * get hours and minutes
	 */
	private void getHoursMin() {

		iHour = timePicker.getCurrentHour();
		iMin = timePicker.getCurrentMinute();

		timePicker.setOnTimeChangedListener(new OnTimeChangedListener() {

			@Override
			public void onTimeChanged(TimePicker view, int hourOfDay, int minute) {
				// TODO Auto-generated method stub

				iHour = hourOfDay;
				iMin = minute;
			}
		});

		ok.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {

				if (Validate() == true) {
					// TODO Auto-generated method stub

					Intent intent = new Intent();
					intent.putExtra("date", GetDateTime());
					setResult(RESULT_OK, intent);
					finish();
				}
			}
		});

		cancel = (Button) findViewById(R.id.cancel);

		cancel.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				finish();
			}
		});
	}

	/**
	 * Get date and time
	 * 
	 * @return
	 */
	private String GetDateTime() {

		try {
			iHour = timePicker.getCurrentHour();
			iMin = timePicker.getCurrentMinute();

			String date = datePicker.getDayOfMonth() + "-"
					+ (datePicker.getMonth() + 1) + "-" + datePicker.getYear();

			String ampm = " am";

			if (iHour == 0) {
				iHour = 12;
			} else if (iHour == 12) {
				iHour = 24;
			}

			if (iHour > 12) {
				iHour = iHour - 12;
				ampm = " pm";
			} else {
			}

			String sHour = "";

			if (iHour < 10) {
				sHour = "0" + iHour;
			} else {
				sHour = iHour + "";
			}

			String sMin = "";

			if (iMin < 10) {

				sMin = "0" + iMin;
			} else {
				sMin = iMin + "";
			}

			String time = " " + sHour + ":" + sMin + ampm/* + ":" +iSec */;

			String datetime = date + time;

			return datetime;

		} catch (Exception exp) {

			return "";
		}
	}

	/**
	 * Validate fields
	 * 
	 * @return
	 */
	private boolean Validate() {
		try {

			Log.d("predate", "predate " + predate);

			if (predate != null && predate.length() != 0) {

				Log.d("predate1", "predate1 " + predate);

				SimpleDateFormat formatter = new SimpleDateFormat(
						"dd-MM-yyyy hh:mm a"); // yyyy-MM-dd HH:mm

				Log.d("predate2", "predate2 " + predate);

				if (predate != null && predate.length() > 0) {
					Log.d("predate3", "predate3 " + predate);
					Date objpredate = formatter.parse(predate);
					Date objCurrent = formatter.parse(GetDateTime());

					Log.d("predate4", "predate4 " + predate);
					if (objpredate.compareTo(objCurrent) >= 0) {

						Log.d("predate5", "predate5 " + predate);
						Toast.makeText(this,
								"Date must be greater then " + predate,
								Toast.LENGTH_LONG).show();
						return false;
					}
				}
			}

		} catch (Exception exp) {
			return false;
		}

		return true;
	}
}
