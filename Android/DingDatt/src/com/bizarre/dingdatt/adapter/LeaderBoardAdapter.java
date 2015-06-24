package com.bizarre.dingdatt.adapter;

import java.util.ArrayList;
import java.util.HashMap;

import com.bizarre.dingdatt.R;
import com.bizarre.dingdatt.imageloader.ImageLoader;
import com.bizarre.dingdatt.pojo.LeaderBoardpojo;

import android.app.Activity;
import android.app.ProgressDialog;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.graphics.Typeface;
import android.media.MediaMetadataRetriever;
import android.os.AsyncTask;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

public class LeaderBoardAdapter extends BaseAdapter {
	Activity context;
	LayoutInflater inflater;
	ArrayList<LeaderBoardpojo> leaderBoardpojos = new ArrayList<LeaderBoardpojo>();
	String type = "";

	public LeaderBoardAdapter(Activity context,
			ArrayList<LeaderBoardpojo> leaderBoardpojos, String type) {
		this.context = context;
		inflater = context.getLayoutInflater();
		this.leaderBoardpojos = leaderBoardpojos;
		this.type = type;
	}

	@Override
	public int getCount() {
		// TODO Auto-generated method stub

		return leaderBoardpojos.size();
	}

	@Override
	public Object getItem(int arg0) {
		// TODO Auto-generated method stub

		return leaderBoardpojos.get(arg0).getName();
	}

	@Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub

		return position;
	}

	@Override
	public View getView(int pos, View convertView, ViewGroup parent) {

		convertView = inflater.inflate(R.layout.leader_board_adapter, null);

		TextView name = (TextView) convertView.findViewById(R.id.name);
		LinearLayout picture = (LinearLayout) convertView
				.findViewById(R.id.picture);
		TextView position = (TextView) convertView.findViewById(R.id.position);
		TextView votes = (TextView) convertView.findViewById(R.id.votes);

		name.setText(leaderBoardpojos.get(pos).getName());

		if (pos == 0) {

			convertView.setBackgroundColor(Color.parseColor("#993A3A3A"));
			name.setTextColor(Color.parseColor("#ffffff"));
			name.setTypeface(null, Typeface.BOLD);
			position.setTextColor(Color.parseColor("#ffffff"));
			votes.setTextColor(Color.parseColor("#ffffff"));

			TextView textView = new TextView(context);
			textView.setText(leaderBoardpojos.get(pos).getPicture());
			textView.setTextColor(Color.parseColor("#ffffff"));
			textView.setTypeface(null, Typeface.BOLD);
			picture.setGravity(Gravity.CENTER);

			picture.addView(textView);

		} else {
			if (type.equalsIgnoreCase("t") == true) {

				TextView textView = new TextView(context);
				textView.setText(leaderBoardpojos.get(pos).getPicture());

				picture.addView(textView);

			} else if (type.equalsIgnoreCase("p") == true) {

				ImageView imageView = new ImageView(context);

				int loader = R.drawable.avator;
				ImageLoader imgLoader = new ImageLoader(context);
				imgLoader.DisplayImage(leaderBoardpojos.get(pos).getPicture(),
						loader, imageView);

				picture.addView(imageView);

			} else {
				ImageView imageView = new ImageView(context);
				imageView.setBackgroundResource(R.drawable.video_icon);
				picture.addView(imageView);

				// new
				// VideoAsync(imageView).execute(leaderBoardpojos.get(pos).getPicture());
			}
		}

		position.setText(leaderBoardpojos.get(pos).getPosition());
		votes.setText(leaderBoardpojos.get(pos).getVotes());

		return convertView;
	}
	/*
	 * class VideoAsync extends AsyncTask<String, Bitmap, Bitmap> {
	 * ProgressDialog dialog; ImageView imageView;
	 * 
	 * public VideoAsync(ImageView imageView) { // TODO Auto-generated
	 * constructor stub
	 * 
	 * this.imageView = imageView; }
	 * 
	 * @Override protected void onPreExecute() { // TODO Auto-generated method
	 * stub super.onPreExecute();
	 * 
	 * dialog = ProgressDialog.show(context, "",
	 * context.getString(R.string.processing_please_wait)); }
	 * 
	 * @Override protected void onPostExecute(Bitmap result) { // TODO
	 * Auto-generated method stub super.onPostExecute(result);
	 * 
	 * imageView.setImageBitmap(result); dialog.dismiss(); }
	 * 
	 * @Override protected Bitmap doInBackground(String... params) { // TODO
	 * Auto-generated method stub
	 * 
	 * MediaMetadataRetriever mediaMetadataRetriever = new
	 * MediaMetadataRetriever(); mediaMetadataRetriever.setDataSource(params[0],
	 * new HashMap<String, String>()); Bitmap bm =
	 * mediaMetadataRetriever.getFrameAtTime(100);
	 * 
	 * return bm; } }
	 */
}
