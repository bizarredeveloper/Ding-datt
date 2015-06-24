package com.bizarre.dingdatt.adapter;

import java.util.ArrayList;

import com.bizarre.dingdatt.MyProfileFragment;
import com.bizarre.dingdatt.R;
import com.bizarre.dingdatt.imageloader.ImageLoader;
import com.bizarre.dingdatt.strings.LocalData;

import android.app.Activity;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.BaseAdapter;

import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

public class GroupAdapter extends BaseAdapter {
	Activity context;
	LayoutInflater inflater;
	ArrayList<MyProfileFragment.GroupPojo> followPojos = new ArrayList<MyProfileFragment.GroupPojo>();
	int owner = 0;
	JoinClick click;
	public static String EDIT_GROUP = "Edit Group (Owner)";
	public static String JOIN = "Join";
	public static String MEMBER = "Member";

	public GroupAdapter(Activity context,
			ArrayList<MyProfileFragment.GroupPojo> followPojos) {
		this.context = context;
		inflater = context.getLayoutInflater();
		this.followPojos = followPojos;

		EDIT_GROUP = context.getString(R.string.edit_group_owner);
		JOIN = context.getString(R.string.join);
		MEMBER = context.getString(R.string.member);
	}

	public GroupAdapter(Activity context,
			ArrayList<MyProfileFragment.GroupPojo> followPojos, int owner,
			JoinClick click) {
		this.context = context;
		inflater = context.getLayoutInflater();
		this.followPojos = followPojos;
		this.owner = owner;
		this.click = click;
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
		TextView type = (TextView) convertView.findViewById(R.id.type);

		LinearLayout type_layout = (LinearLayout) convertView
				.findViewById(R.id.type_layout);
		type_layout.setVisibility(View.VISIBLE);
		type.setText(followPojos.get(pos).getType());

		LocalData data = new LocalData(context);

		if (data.GetS("userid").equalsIgnoreCase(
				followPojos.get(pos).getUserid()) == true) {

			unfollow.setText(EDIT_GROUP);
			unfollow.setTextColor(Color.parseColor("#0a95d6"));
		} else if (followPojos.get(pos).getMember() == 0) {

			unfollow.setText(JOIN);
			unfollow.setTextColor(Color.parseColor("#50C016"));
		} else {

			unfollow.setText(MEMBER);
			unfollow.setTextColor(Color.parseColor("#0a95d6"));
		}

		unfollow.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				if (click != null)
					click.onJoinClick(((TextView) v).getText().toString(), v
							.getTag().toString());
			}
		});
		unfollow.setTag(pos + "");

		name.setText(followPojos.get(pos).getName());

		int loader = R.drawable.avator;
		ImageLoader imageLoader = new ImageLoader(context);
		imageLoader.DisplayImage(followPojos.get(pos).getPicture(), loader,
				picture);

		if (owner == 1) {

			LinearLayout owner_layout = (LinearLayout) convertView
					.findViewById(R.id.owner_layout);
			owner_layout.setVisibility(View.VISIBLE);

			TextView owner = (TextView) convertView.findViewById(R.id.owner);
			owner.setText(followPojos.get(pos).getUsername());
		} else if (owner == 0) {

			LinearLayout owner_layout = (LinearLayout) convertView
					.findViewById(R.id.owner_layout);
			owner_layout.setVisibility(View.GONE);
		}

		return convertView;
	}

	public interface JoinClick {

		public void onJoinClick(String tag, String pos);
	}
}
