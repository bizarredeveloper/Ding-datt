package com.bizarre.dingdatt.adapter;

import java.util.ArrayList;

import com.bizarre.dingdatt.NotificationFragment.GetAdminRequest;
import com.bizarre.dingdatt.imageloader.ImageLoader;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.R;

import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.CheckBox;
import android.widget.ImageView;
import android.widget.TextView;

/**
 * 
 * @author karthik
 *
 */
public class AdminRequestAdapter extends BaseAdapter {
	Activity context;
	LayoutInflater inflater;
	ArrayList<GetAdminRequest> list = new ArrayList<GetAdminRequest>();
	String type = "";
	ListSelected listSelected;
	ArrayList<String> selectedlist = new ArrayList<String>();
	ArrayList<String> userids = new ArrayList<String>();
	int iOwner = 0;

	public AdminRequestAdapter(Activity context,
			ArrayList<GetAdminRequest> list, ListSelected listSelected,
			int owner) {
		this.context = context;
		inflater = context.getLayoutInflater();
		this.list = list;
		this.listSelected = listSelected;
		this.iOwner = owner;
	}

	@Override
	public int getCount() {
		// TODO Auto-generated method stub

		return list.size();
	}

	@Override
	public Object getItem(int arg0) {
		// TODO Auto-generated method stub

		return list.get(arg0).getGroupid();
	}

	@Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub

		return position;
	}

	@Override
	public View getView(int pos, View convertView, ViewGroup parent) {

		convertView = inflater.inflate(R.layout.admin_request, null);

		TextView name = (TextView) convertView.findViewById(R.id.name);
		ImageView picture = (ImageView) convertView.findViewById(R.id.picture);
		CheckBox checkbox = (CheckBox) convertView.findViewById(R.id.checkbox);
		TextView owner = (TextView) convertView.findViewById(R.id.owner);
		TextView ownertext = (TextView) convertView
				.findViewById(R.id.ownertext);

		name.setText(list.get(pos).getGroupname());

		int loader = R.drawable.avator;
		ImageLoader imageLoader = new ImageLoader(context);
		imageLoader
				.DisplayImage(list.get(pos).getGroupimage(), loader, picture);

		checkbox.setTag(list.get(pos).getGroupid());

		if (iOwner == 0) {

			owner.setText(context.getString(R.string.member_colon));
			name.setTag(list.get(pos).getUserid());

		} else {

			owner.setText(context.getString(R.string.owner_colon));
			LocalData data = new LocalData(context);

			name.setTag(data.GetS("userid"));
		}

		ownertext.setText(list.get(pos).getUsername());

		name.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				Selection(v);
			}
		});

		picture.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				Selection(v);
			}
		});

		checkbox.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				CheckBox box = (CheckBox) v;
				ViewGroup parent = null;
				parent = (ViewGroup) v.getParent();
				TextView name = (TextView) parent.findViewById(R.id.name);

				if (box.isChecked() == false) {
					selectedlist.remove(selectedlist.indexOf(box.getTag()
							.toString()));
					userids.remove(userids.indexOf(name.getTag().toString()));
					listSelected.onListSelected(selectedlist, userids);

				} else if (box.isChecked() == true) {
					selectedlist.add(box.getTag().toString());
					userids.add(name.getTag().toString());
					listSelected.onListSelected(selectedlist, userids);
				}
			}
		});

		owner.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				Selection(v);
			}
		});

		for (int i = 0; i < selectedlist.size(); i++) {

			if (list.get(pos).getGroupid()
					.equalsIgnoreCase(selectedlist.get(i)) == true) {
				checkbox.setChecked(true);
				break;
			} else {
				checkbox.setChecked(false);
			}
		}

		ownertext.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				Selection(v);
			}
		});

		return convertView;
	}

	/**
	 * 
	 * @author karthik
	 *
	 */
	public interface ListSelected {
		public void onListSelected(ArrayList<String> selectedgroupids,
				ArrayList<String> userids);
	}

	private void Selection(View v) {

		ViewGroup parent = null;

		if (v.getId() == R.id.picture || v.getId() == R.id.checkbox) {
			parent = (ViewGroup) v.getParent();

		} else if (v.getId() == R.id.name) {
			parent = (ViewGroup) ((ViewGroup) v.getParent()).getParent();

		} else if (v.getId() == R.id.owner || v.getId() == R.id.ownertext) {
			parent = (ViewGroup) ((ViewGroup) ((ViewGroup) v.getParent())
					.getParent()).getParent();

		}

		CheckBox box = (CheckBox) parent.findViewById(R.id.checkbox);
		TextView name = (TextView) parent.findViewById(R.id.name);

		if (box.isChecked() == true) {
			box.setChecked(false);
			selectedlist.remove(selectedlist.indexOf(box.getTag().toString()));
			userids.remove(userids.indexOf(name.getTag().toString()));
			listSelected.onListSelected(selectedlist, userids);

		} else if (box.isChecked() == false) {
			box.setChecked(true);
			selectedlist.add(box.getTag().toString());
			userids.add(name.getTag().toString());
			listSelected.onListSelected(selectedlist, userids);

		}
	}
}
