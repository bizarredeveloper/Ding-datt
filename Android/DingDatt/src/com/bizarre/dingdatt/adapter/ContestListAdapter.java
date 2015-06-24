package com.bizarre.dingdatt.adapter;

import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import com.bizarre.dingdatt.R;
import com.bizarre.dingdatt.imageloader.ImageLoader;
import com.bizarre.dingdatt.pojo.ContestDetails;

import android.app.Activity;
import android.app.ProgressDialog;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

public class ContestListAdapter extends BaseAdapter {
	static Activity context;
	LayoutInflater inflater;
	ArrayList<ContestDetails> contestDetails = new ArrayList<ContestDetails>();

	public ContestListAdapter(Activity context, ArrayList<ContestDetails> asList) {
		this.context = context;
		inflater = context.getLayoutInflater();
		this.contestDetails = asList;
	}

	@Override
	public int getCount() {
		// TODO Auto-generated method stub

		return contestDetails.size();
	}

	@Override
	public Object getItem(int arg0) {
		// TODO Auto-generated method stub

		return contestDetails.get(arg0);
	}

	@Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub

		return position;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		// TODO Auto-generated method stub

		try {

			DateFormat formatter = new SimpleDateFormat("dd-MM-yyyy hh:mm a");// yyyy-MM-dd
																				// HH:mm
			SimpleDateFormat newFormat = new SimpleDateFormat("dd-MMM-yyyy");

			View rowView = inflater.inflate(R.layout.contest_adapter, null,
					true);

			ImageView image = (ImageView) rowView.findViewById(R.id.image);
			TextView contest_name = (TextView) rowView
					.findViewById(R.id.contest_name);
			TextView start_date = (TextView) rowView
					.findViewById(R.id.start_date);
			TextView end_date = (TextView) rowView.findViewById(R.id.end_date);
			TextView prize = (TextView) rowView.findViewById(R.id.prize);
			ImageView ding = (ImageView) rowView.findViewById(R.id.ding);

			if (contestDetails.get(position).getContestparticipantid() == true)
				ding.setVisibility(View.VISIBLE);
			else
				ding.setVisibility(View.INVISIBLE);

			int loader = R.drawable.loader;
			String image_url = contestDetails.get(position).getThemephoto();
			ImageLoader imgLoader = new ImageLoader(context);
			imgLoader.DisplayImage(image_url, loader, image);

			contest_name
					.setText(contestDetails.get(position).getContest_name());

			Date date1 = (Date) formatter.parse(contestDetails.get(position)
					.getConteststartdate());
			String sContestStartDate = newFormat.format(date1);

			start_date.setText(sContestStartDate);

			Date date2 = (Date) formatter.parse(contestDetails.get(position)
					.getContestenddate());
			String sContestEnddate = newFormat.format(date2);

			end_date.setText(sContestEnddate);

			prize.setText(contestDetails.get(position).getPrize());

			return rowView;
		} catch (Exception exception) {
			return null;
		}
	}
}
