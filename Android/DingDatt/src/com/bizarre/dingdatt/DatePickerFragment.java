package com.bizarre.dingdatt;

import java.util.Calendar;

import android.app.DatePickerDialog;
import android.app.Dialog;
import android.os.Bundle;
import android.support.v4.app.DialogFragment;
import android.widget.DatePicker;

public class DatePickerFragment extends DialogFragment implements
		DatePickerDialog.OnDateSetListener {

	DateSelectListener dateSelectListener;

	public DatePickerFragment(DateSelectListener dateSelectListener) {
		this.dateSelectListener = dateSelectListener;
	}

	@Override
	public Dialog onCreateDialog(Bundle savedInstanceState) {
		// Use the current date as the default date in the picker
		final Calendar c = Calendar.getInstance();
		int year = c.get(Calendar.YEAR);
		int month = c.get(Calendar.MONTH);
		int day = c.get(Calendar.DAY_OF_MONTH);

		// Create a new instance of DatePickerDialog and return it
		return new DatePickerDialog(getActivity(), this, year, month, day);
	}

	public void onDateSet(DatePicker view, int year, int month, int day) {
		// Do something with the date chosen by the user

		// Show selected date
		StringBuilder sDate = (new StringBuilder().append(day).append("-")
				.append(month + 1).append("-").append(year).append(""));

		dateSelectListener.onDateSelect(sDate.toString());
	}

	public interface DateSelectListener {
		public void onDateSelect(String sDate);
	}
}
