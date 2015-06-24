package com.bizarre.dingdatt.adapter;

import java.util.ArrayList;

import com.bizarre.dingdatt.R;
import com.bizarre.dingdatt.MemberListFragment.GroupMembers;
import com.bizarre.dingdatt.imageloader.ImageLoader;
import com.bizarre.dingdatt.strings.LocalData;

import android.app.Activity;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.CheckBox;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

public class MemberInviteAdapter extends BaseAdapter {
	Activity context;
	LayoutInflater inflater;
	ArrayList<GroupMembers> groupmemberPojos = new ArrayList<GroupMembers>();
	ArrayList<String> selecttext = new ArrayList<String>();
	SelectedList list;

	public interface SelectedList {
		public void onSelectedList(ArrayList<String> selecttext);
	}

	public MemberInviteAdapter(Activity context,
			ArrayList<GroupMembers> groupmemberPojos, SelectedList list) {
		this.context = context;
		inflater = context.getLayoutInflater();
		this.groupmemberPojos = groupmemberPojos;
		this.list = list;
	}

	@Override
	public int getCount() {
		// TODO Auto-generated method stub

		return groupmemberPojos.size();
	}

	@Override
	public Object getItem(int arg0) {
		// TODO Auto-generated method stub

		return groupmemberPojos.get(arg0).getId();
	}

	@Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub

		return position;
	}

	private static String ADMIN_USER_ID = "1";

	@Override
	public View getView(int pos, View convertView, ViewGroup parent) {

		convertView = inflater.inflate(R.layout.member_invite_adapter, null);

		TextView name = (TextView) convertView.findViewById(R.id.name);
		ImageView picture = (ImageView) convertView.findViewById(R.id.picture);
		TextView type = (TextView) convertView.findViewById(R.id.type);
		CheckBox checkbox = (CheckBox) convertView.findViewById(R.id.checkbox);
		checkbox.setTag(groupmemberPojos.get(pos).getId());

		name.setText(groupmemberPojos.get(pos).getName());

		ImageLoader imageLoader = new ImageLoader(context);
		int loader = R.drawable.avator;
		imageLoader.DisplayImage(groupmemberPojos.get(pos).getPicture(),
				loader, picture);

		LocalData data = new LocalData(context);

		if (data.GetS("userid").equalsIgnoreCase(
				groupmemberPojos.get(pos).getUser_id()) == true
				|| groupmemberPojos.get(pos).getUser_id()
						.equalsIgnoreCase(ADMIN_USER_ID) == true) {
			checkbox.setVisibility(View.INVISIBLE);
			type.setVisibility(View.INVISIBLE);
		} else {
			checkbox.setVisibility(View.VISIBLE);
			type.setVisibility(View.VISIBLE);
		}

		/*
		 * if(groupmemberPojos.get(pos).getAdmin_user().equalsIgnoreCase(
		 * groupmemberPojos.get(pos).getUser_id()) == true) {
		 * 
		 * type.setText("Group Owner");
		 * type.setTextColor(Color.parseColor("#ffffff"));
		 * 
		 * } else
		 */if (groupmemberPojos.get(pos).getInvite() == 1) {
			type.setTextColor(Color.parseColor("#ff0000"));
			type.setText("Uninvite");

		} else if (groupmemberPojos.get(pos).getInvite() == 0) {
			type.setTextColor(Color.parseColor("#00ff00"));
			type.setText("Invite");

		}

		checkbox.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				CheckBox box = ((CheckBox) v);
				if (box.isChecked() == true) {
					selecttext.add(box.getTag().toString());
					list.onSelectedList(selecttext);
				} else {
					selecttext.remove(selecttext.indexOf(box.getTag()
							.toString()));
					list.onSelectedList(selecttext);
				}
			}
		});

		return convertView;
	}
}
