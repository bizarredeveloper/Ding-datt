package com.bizarre.dingdatt.adapter;

import java.util.ArrayList;

import com.bizarre.dingdatt.MyProfileFragment;
import com.bizarre.dingdatt.R;
import com.bizarre.dingdatt.imageloader.ImageLoader;

import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

public class FollowAdapter extends BaseAdapter {
	Activity context;
	LayoutInflater inflater;
	ArrayList<MyProfileFragment.FollowPojo> followPojos = new ArrayList<MyProfileFragment.FollowPojo>();
	int followtype = 0;
	ClickUnfollow clickUnfollow;

	public FollowAdapter(Activity context,
			ArrayList<MyProfileFragment.FollowPojo> followPojos,
			int followtype, ClickUnfollow clickUnfollow) {
		this.context = context;
		inflater = context.getLayoutInflater();
		this.followPojos = followPojos;
		this.clickUnfollow = clickUnfollow;
		this.followtype = followtype;
	}

	@Override
	public int getCount() {
		// TODO Auto-generated method stub

		return followPojos.size();
	}

	@Override
	public Object getItem(int arg0) {
		// TODO Auto-generated method stub

		return followPojos.get(arg0).getId();
	}

	@Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub

		return position;
	}

	@Override
	public View getView(int pos, View convertView, ViewGroup parent) {

		convertView = inflater.inflate(R.layout.follow_adapter, null);

		TextView name = (TextView) convertView.findViewById(R.id.name);
		ImageView picture = (ImageView) convertView.findViewById(R.id.picture);
		TextView unfollow = (TextView) convertView.findViewById(R.id.unfollow);
		unfollow.setTag(followPojos.get(pos).getId());

		if (followtype == 0) {

			unfollow.setVisibility(View.GONE);
		} else {

			unfollow.setVisibility(View.VISIBLE);
		}

		name.setText(followPojos.get(pos).getName());

		int loader = R.drawable.avator;
		ImageLoader imageLoader = new ImageLoader(context);
		imageLoader.DisplayImage(followPojos.get(pos).getProfilepic(), loader,
				picture);

		unfollow.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				clickUnfollow.onClickUnfollow(v.getTag().toString());
			}
		});

		return convertView;
	}

	public interface ClickUnfollow {

		public void onClickUnfollow(String userid);
	}
}
