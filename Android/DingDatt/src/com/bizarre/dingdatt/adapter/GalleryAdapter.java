package com.bizarre.dingdatt.adapter;

import java.util.ArrayList;
import java.util.HashMap;

import com.bizarre.dingdatt.MyProfileFragment;
import com.bizarre.dingdatt.R;
import com.bizarre.dingdatt.imageloader.ImageLoader;

import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Bitmap;
import android.media.MediaMetadataRetriever;
import android.os.AsyncTask;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

public class GalleryAdapter extends BaseAdapter {
	private ArrayList<String> images = new ArrayList<String>();
	private ArrayList<String> topics = new ArrayList<String>();
	private ArrayList<String> ids = new ArrayList<String>();
	private ArrayList<String> dummy = new ArrayList<String>();
	private String type = "";
	private ArrayList<MyProfileFragment.MyHistory> myHistories = new ArrayList<MyProfileFragment.MyHistory>();

	private Context context;
	private LayoutInflater mInflater;

	public GalleryAdapter(Context context, ArrayList<String> images,
			ArrayList<String> topics, ArrayList<String> ids, String type) {
		this.context = context;
		mInflater = LayoutInflater.from(context);
		this.images = images;
		this.topics = topics;
		this.ids = ids;
		this.type = type;

		if (type.equalsIgnoreCase("p")) {
			dummy = images;
		} else if (type.equalsIgnoreCase("v")) {
			dummy = images;
		} else {
			dummy = topics;
		}
	}

	public GalleryAdapter(Context context,
			ArrayList<MyProfileFragment.MyHistory> myHistories) {
		this.context = context;
		mInflater = LayoutInflater.from(context);

		this.myHistories = myHistories;

		for (int i = 0; i < myHistories.size(); i++) {
			if (myHistories.get(i).getContesttype().equalsIgnoreCase("p")) {
				dummy.add(myHistories.get(i).getUploadfile());
			} else if (myHistories.get(i).getContesttype()
					.equalsIgnoreCase("v")) {
				dummy.add(myHistories.get(i).getUploadfile());
			} else {
				dummy.add(myHistories.get(i).getUploadtopic());
			}
		}
	}

	@Override
	public int getCount() {
		return dummy.size();
	}

	@Override
	public String getItem(int i) {
		return dummy.get(i);
	}

	@Override
	public long getItemId(int i) {
		return i;
	}

	@Override
	public View getView(int i, View view, ViewGroup viewGroup) {

		View v = view;

		String sType = "";
		String sTopic = "";
		String sId = "";
		String sImage = "";

		if (myHistories.size() > 0) {

			sType = myHistories.get(i).getContesttype();
			sTopic = myHistories.get(i).getUploadtopic();
			sId = Integer.toString(myHistories.get(i).getId());
			sImage = myHistories.get(i).getUploadfile();

		} else {

			sType = this.type;
			sTopic = topics.get(i);
			sId = ids.get(i);
			sImage = images.get(i);
		}

		ImageView picture;
		ImageView video;
		TextView topic;
		TextView contest_name;

		if (v == null) {
			v = mInflater.inflate(R.layout.adapter_gallery, viewGroup, false);
		}

		picture = (ImageView) v.findViewById(R.id.picture);
		video = (ImageView) v.findViewById(R.id.video);
		topic = (TextView) v.findViewById(R.id.topic);
		contest_name = (TextView) v.findViewById(R.id.contes_name);

		picture.setVisibility(View.VISIBLE);
		video.setVisibility(View.VISIBLE);
		topic.setVisibility(View.VISIBLE);

		if (myHistories.size() > 0) {
			contest_name.setText(myHistories.get(i).getContest_name());
		} else {
			contest_name.setVisibility(View.GONE);
		}

		if (sType.equalsIgnoreCase("t") == true) {

			topic.setText(sTopic);
			topic.setTag(sId);

			video.setVisibility(View.GONE);
			topic.setVisibility(View.VISIBLE);
			picture.setVisibility(View.GONE);

		} else if (sType.equalsIgnoreCase("p") == true) {

			int loader = R.drawable.image_loader;
			ImageLoader imgLoader = new ImageLoader(context);
			imgLoader.DisplayImage(sImage, loader, picture);
			picture.setTag(sId);

			video.setVisibility(View.GONE);
			topic.setVisibility(View.GONE);
			picture.setVisibility(View.VISIBLE);
		} else if (sType.equalsIgnoreCase("v") == true) {
			// new VideoAsync(picture).execute(sImage);
			video.setImageResource(R.drawable.video_icon);

			video.setVisibility(View.VISIBLE);
			topic.setVisibility(View.GONE);
			picture.setVisibility(View.GONE);
		}

		return v;
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
	 * Bitmap bm;
	 * 
	 * try { if(params[0].length() != 0) { MediaMetadataRetriever
	 * mediaMetadataRetriever = new MediaMetadataRetriever();
	 * mediaMetadataRetriever.setDataSource(params[0], new HashMap<String,
	 * String>()); bm = mediaMetadataRetriever.getFrameAtTime(100); } else {
	 * return null; } } catch(Exception exp) { return null; }
	 * 
	 * return bm; } }
	 */
}