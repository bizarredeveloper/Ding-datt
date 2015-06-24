package com.bizarre.dingdatt.adapter;

import java.util.ArrayList;

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

public class CommentsAdapter extends BaseAdapter {
	static Activity context;
	LayoutInflater inflater;
	CommentReplyClick click;

	ArrayList<String> profilepics = new ArrayList<String>();
	ArrayList<String> ids = new ArrayList<String>();
	ArrayList<String> comments = new ArrayList<String>();
	ArrayList<String> user = new ArrayList<String>();
	ArrayList<String> acommentid = new ArrayList<String>();

	public CommentsAdapter(Activity context, ArrayList<String> profilepics,
			ArrayList<String> ids, ArrayList<String> comments,
			ArrayList<String> user, ArrayList<String> commentid,
			CommentReplyClick click) {
		this.context = context;
		inflater = context.getLayoutInflater();
		this.profilepics = profilepics;
		this.ids = ids;
		this.comments = comments;
		this.user = user;
		this.click = click;
		this.acommentid = commentid;
	}

	@Override
	public int getCount() {
		// TODO Auto-generated method stub

		return ids.size();
	}

	@Override
	public Object getItem(int arg0) {
		// TODO Auto-generated method stub

		return ids.get(arg0);
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
			convertView = inflater.inflate(R.layout.comments_row, parent);

			int loader = R.drawable.avator;
			ImageLoader imageLoader = new ImageLoader(context);
			ImageView imageView = (ImageView) convertView
					.findViewById(R.id.profileimage);
			imageLoader.DisplayImage(profilepics.get(position), loader,
					imageView);

			TextView message = (TextView) convertView
					.findViewById(R.id.message);
			message.setText(comments.get(position));

			TextView profilename = (TextView) convertView
					.findViewById(R.id.profilename);
			profilename.setText(user.get(position));

			TextView reply = (TextView) convertView.findViewById(R.id.reply);
			reply.setTag(position + "");

			if (click == null)
				reply.setVisibility(View.GONE);

			reply.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					// TODO Auto-generated method stub
					int iPos = Integer.parseInt(v.getTag().toString());
					click.onReplyClick(iPos);
				}
			});

			return convertView;
		} catch (Exception exception) {
			return null;
		}
	}
}
