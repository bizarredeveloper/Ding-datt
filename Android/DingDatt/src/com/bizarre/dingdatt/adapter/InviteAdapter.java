package com.bizarre.dingdatt.adapter;

import java.util.ArrayList;

import com.bizarre.dingdatt.R;
import com.bizarre.dingdatt.imageloader.ImageLoader;
import com.bizarre.dingdatt.pojo.GroupFollower;
import com.bizarre.dingdatt.strings.StringURLs;

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

public class InviteAdapter extends BaseAdapter {
	Activity context;
	LayoutInflater inflater;
	ArrayList<GroupFollower> asList = new ArrayList<GroupFollower>();
	int type = -1;
	ArrayList<String> selected_text = new ArrayList<String>();
	InviteLisener lisener;

	public interface InviteLisener {
		public void onInviteListener(String sPos);
	}

	public InviteAdapter(Activity context, ArrayList<GroupFollower> asList,
			int type, InviteLisener lisener) {
		this.context = context;
		inflater = context.getLayoutInflater();
		this.asList = asList;
		this.type = type;
		this.lisener = lisener;
	}

	@Override
	public int getCount() {
		// TODO Auto-generated method stub

		return asList.size();
	}

	@Override
	public Object getItem(int arg0) {
		// TODO Auto-generated method stub

		return asList.get(arg0);
	}

	@Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub

		return position;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		// TODO Auto-generated method stub

		View rowView = inflater.inflate(R.layout.invite_adapter, null, true);

		ImageView image = (ImageView) rowView.findViewById(R.id.image);
		TextView name = (TextView) rowView.findViewById(R.id.name);
		TextView invite1 = (TextView) rowView.findViewById(R.id.invite1);
		ImageView invite2 = (ImageView) rowView.findViewById(R.id.invite2);

		int loader = R.drawable.avator;
		ImageLoader imgLoader = new ImageLoader(context);

		if (type == 0) {

			name.setText(asList.get(position).getGroupname());

			// if(asList.get(position).getGroupimage().length() > 0)
			imgLoader.DisplayImage(asList.get(position).getGroupimage(),
					loader, image);
		} else {

			name.setText(asList.get(position).getFirstname());

			// if(asList.get(position).getProfilepicture().length() > 0)
			imgLoader.DisplayImage(asList.get(position).getProfilepicture(),
					loader, image);
		}

		invite1.setTag(position + "");

		invite1.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				lisener.onInviteListener(v.getTag().toString());
			}
		});

		if (asList.get(position).getInvite() >= 1) {

			invite1.setText(context.getString(R.string.uninvite));
			invite1.setTextColor(Color.parseColor("#FF0000"));
			invite2.setBackgroundResource(R.drawable.invited_tick_icon);
		} else if (asList.get(position).getInvite() == 0) {

			invite1.setText(context.getString(R.string.invite));
			invite1.setTextColor(Color.parseColor("#5AB6D6"));
			invite2.setBackgroundResource(R.drawable.invite_send_icon);
		} else if (asList.get(position).getInvite() == -1) {

			invite1.setText(context.getString(R.string.partial_invite));
			invite1.setTextColor(Color.parseColor("#0000FF"));
			invite2.setBackgroundResource(0);
		}

		return rowView;
	}
}
