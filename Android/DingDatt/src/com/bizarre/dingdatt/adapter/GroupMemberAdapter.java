package com.bizarre.dingdatt.adapter;

import java.util.ArrayList;

import com.bizarre.dingdatt.MemberListFragment.GroupMembers;
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
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

public class GroupMemberAdapter extends BaseAdapter {
	Activity context;
	LayoutInflater inflater;
	ArrayList<GroupMembers> groupmemberPojos = new ArrayList<GroupMembers>();
	int owner = 0;
	public static String GROUP_OWNER = "Group Owner";
	public static String REMOVE = "Remove";
	public static String EXITGROUP = "Exit from Group";
	public static String ADD_MEMBER = "Add Member";

	ClickButtonFromMember buttonFromMember;

	public GroupMemberAdapter(Activity context,
			ArrayList<GroupMembers> groupmemberPojos,
			ClickButtonFromMember buttonFromMember) {
		this.context = context;
		inflater = context.getLayoutInflater();
		this.groupmemberPojos = groupmemberPojos;
		this.buttonFromMember = buttonFromMember;

		GROUP_OWNER = context.getString(R.string.group_owner);
		REMOVE = context.getString(R.string.remove);
		EXITGROUP = context.getString(R.string.exit_from_group);
		ADD_MEMBER = context.getString(R.string.add_member);
	}

	@Override
	public int getCount() {
		// TODO Auto-generated method stub

		return groupmemberPojos.size();
	}

	@Override
	public Object getItem(int arg0) {
		// TODO Auto-generated method stub

		return groupmemberPojos.get(arg0).getUser_id();
	}

	@Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub

		return position;
	}

	private int addMember = 0;

	@Override
	public View getView(int pos, View convertView, ViewGroup parent) {

		convertView = inflater.inflate(R.layout.group_member_adapter, null);

		TextView name = (TextView) convertView.findViewById(R.id.name);
		ImageView picture = (ImageView) convertView.findViewById(R.id.picture);
		TextView unfollow = (TextView) convertView.findViewById(R.id.event);

		name.setText(groupmemberPojos.get(pos).getName());

		int loader = R.drawable.avator;
		ImageLoader imageLoader = new ImageLoader(context);
		imageLoader.DisplayImage(groupmemberPojos.get(pos).getPicture(),
				loader, picture);

		LocalData data = new LocalData(context);

		if (groupmemberPojos.get(pos).getAdmin_user()
				.equalsIgnoreCase(data.GetS("userid")) == true) {

			if (groupmemberPojos.get(pos).getAdmin_user()
					.equalsIgnoreCase(groupmemberPojos.get(pos).getUser_id())) {

				unfollow.setText(GROUP_OWNER);
				unfollow.setVisibility(View.VISIBLE);
			} else {
				unfollow.setText(REMOVE);
				unfollow.setVisibility(View.VISIBLE);
			}
		} else if (groupmemberPojos.get(pos).getAdmin_user()
				.equalsIgnoreCase(groupmemberPojos.get(pos).getUser_id())) {

			unfollow.setText(GROUP_OWNER);
			unfollow.setVisibility(View.VISIBLE);
		} else if (groupmemberPojos.get(pos).getUser_id()
				.equalsIgnoreCase(data.GetS("userid")) == true) {
			unfollow.setText(EXITGROUP);
			unfollow.setVisibility(View.VISIBLE);
		} else if (addMember == 1) {

			unfollow.setText(ADD_MEMBER);
			unfollow.setVisibility(View.VISIBLE);
		} else {
			unfollow.setVisibility(View.GONE);
			unfollow.setText("");
		}

		unfollow.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				if (buttonFromMember != null)
					buttonFromMember.onClickButtonFromMember(((TextView) v)
							.getText().toString(), v.getTag().toString());
			}
		});

		unfollow.setTag(pos + "");

		return convertView;
	}

	/**
	 * @return the addMember
	 */
	public int getAddMember() {
		return addMember;
	}

	/**
	 * @param addMember
	 *            the addMember to set
	 */
	public void setAddMember(int addMember) {
		this.addMember = addMember;
	}

	public interface ClickButtonFromMember {

		public void onClickButtonFromMember(String tag, String pos);
	}
}
