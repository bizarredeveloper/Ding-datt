package com.bizarre.dingdatt;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONObject;

import com.bizarre.dingdatt.adapter.AdminRequestAdapter;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;

import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

/**
 * 
 * @author karthik
 *
 */
public class NotificationFragment extends Fragment {

	private FragmentActivity context;
	private View rootView;
	LayoutInflater inflater;
	TextView adminreqcount;
	TextView adminrequest;
	TextView joinreqcount;
	TextView joinrequest;
	ListView listview;
	Button accept;
	Button reject;
	ArrayList<GetAdminRequest> adminRequests = new ArrayList<NotificationFragment.GetAdminRequest>();
	int selectedtab = 0;

	public NotificationFragment(FragmentActivity context) {

		this.context = context;
	}

	@Override
	public View onCreateView(final LayoutInflater inflater1,
			ViewGroup container, Bundle savedInstanceState) {
		this.inflater = inflater1;
		rootView = inflater.inflate(R.layout.notification, container, false);

		Main.SetAdvertisment(rootView);

		adminreqcount = (TextView) rootView.findViewById(R.id.adminreqcount);
		adminrequest = (TextView) rootView.findViewById(R.id.adminrequest);
		joinreqcount = (TextView) rootView.findViewById(R.id.joinreqcount);
		joinrequest = (TextView) rootView.findViewById(R.id.joinrequest);
		listview = (ListView) rootView.findViewById(R.id.listview);
		accept = (Button) rootView.findViewById(R.id.accept);
		reject = (Button) rootView.findViewById(R.id.reject);

		selectedtab = 0;
		GetAdminRequestNotification();

		adminreqcount.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				selectedtab = 0;

				LinearLayout layout1 = (LinearLayout) v.getParent();
				LinearLayout layout2 = (LinearLayout) joinreqcount.getParent();

				layout1.setBackgroundColor(Color.parseColor("#45B3AA"));
				layout2.setBackgroundColor(Color.parseColor("#21739A"));

				mselectedgroupids = new ArrayList<String>();
				muserids = new ArrayList<String>();

				GetAdminRequestNotification();
			}
		});

		adminrequest.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				selectedtab = 0;

				LinearLayout layout1 = (LinearLayout) v.getParent();
				LinearLayout layout2 = (LinearLayout) joinreqcount.getParent();

				layout1.setBackgroundColor(Color.parseColor("#45B3AA"));
				layout2.setBackgroundColor(Color.parseColor("#21739A"));

				mselectedgroupids = new ArrayList<String>();
				muserids = new ArrayList<String>();

				GetAdminRequestNotification();
			}
		});

		joinreqcount.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				selectedtab = 1;

				LinearLayout layout1 = (LinearLayout) v.getParent();
				LinearLayout layout2 = (LinearLayout) adminreqcount.getParent();

				layout1.setBackgroundColor(Color.parseColor("#45B3AA"));
				layout2.setBackgroundColor(Color.parseColor("#21739A"));

				mselectedgroupids = new ArrayList<String>();
				muserids = new ArrayList<String>();

				GetMemberRequestList();
			}
		});

		joinrequest.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				selectedtab = 1;

				LinearLayout layout1 = (LinearLayout) v.getParent();
				LinearLayout layout2 = (LinearLayout) adminreqcount.getParent();

				layout1.setBackgroundColor(Color.parseColor("#45B3AA"));
				layout2.setBackgroundColor(Color.parseColor("#21739A"));

				mselectedgroupids = new ArrayList<String>();
				muserids = new ArrayList<String>();

				GetMemberRequestList();
			}
		});

		accept.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (mselectedgroupids.size() > 0)
					UpdateNotification("accept");
				else
					Toast.makeText(context, "Please select first.",
							Toast.LENGTH_LONG).show();

			}
		});

		reject.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				if (mselectedgroupids.size() > 0)
					UpdateNotification("reject");
				else
					Toast.makeText(context, "Please select first.",
							Toast.LENGTH_LONG).show();
			}
		});

		return rootView;
	}

	/**
	 * Get Notification count from server.
	 * 
	 * @param i
	 */
	private void GetNotificationCount(final int i) {
		try {

			String url = StringURLs.NOTIFICATION_COUNT;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("user_id");
			LocalData data = new LocalData(context);
			values.add(data.GetS("userid"));
			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(context);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {

						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);

							adminreqcount.setText(jsonObject2
									.getInt("admincount") + "");
							joinreqcount.setText(jsonObject2
									.getInt("membercount") + "");

							LocalData data = new LocalData(context);
							data.Update("notificationcount",
									jsonObject2.getInt("admincount")
											+ jsonObject2.getInt("membercount"));

							if (i == 1) {
								Intent intent = new Intent(context,
										NotificationActivity.class);
								startActivity(intent);
								context.finish();
							}

						} else {

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();
						}
					} catch (Exception exp) {

						Toast.makeText(context,
								Main.getStringResourceByName(context, "c100"),
								Toast.LENGTH_LONG).show();
					}
				}
			});
			connectServerImage.setMode(ConnectServerImage.MODE_POST);
			connectServerImage.setNames(names);
			connectServerImage.setValues(values);
			connectServerImage.execute(url);
		} catch (Exception exp) {

			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * Get admin request notification
	 */
	private void GetAdminRequestNotification() {

		GetNotificationCount(0);

		String url = StringURLs.ADMINREQUEST;

		ArrayList<String> names = new ArrayList<String>();
		ArrayList<String> values = new ArrayList<String>();

		names.add("user_id");

		LocalData data = new LocalData(context);

		values.add(data.GetS("userid"));

		ConnectServerImage connectServerImage = new ConnectServerImage();
		connectServerImage.setContext(context);
		;
		connectServerImage.setListener(new ConnectServerListener() {

			@Override
			public void onServerResponse(String sJSON, JSONObject jsonObject) {
				// TODO Auto-generated method stub

				try {

					adminRequests = new ArrayList<NotificationFragment.GetAdminRequest>();

					if (sJSON.length() > 0) {

						JSONObject jsonObject2 = new JSONObject(sJSON);

						if (jsonObject2.getJSONObject("response")
								.getString("success").equalsIgnoreCase("1") == true) {

							JSONArray getadminrequest = jsonObject2
									.getJSONArray("getadminrequest");

							for (int i = 0; i < getadminrequest.length(); i++) {

								GetAdminRequest adminRequest = new GetAdminRequest();

								JSONObject object = getadminrequest
										.getJSONObject(i);

								adminRequest.setGroupid(object
										.getString("group_id"));
								adminRequest.setPicture(object
										.getString("profilepicture"));
								adminRequest.setGroupimage(object
										.getString("groupimage"));
								adminRequest.setGroupname(object
										.getString("groupname"));
								adminRequest.setInvitetype(object
										.getString("invitetype"));
								adminRequest.setUserid(object
										.getString("userid"));
								adminRequest.setUsername(object
										.getString("name"));

								adminRequests.add(adminRequest);
							}

						} else {

							String msgcode = jsonObject.getJSONObject(
									"response").getString("msgcode");

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											msgcode), Toast.LENGTH_LONG).show();
						}

					} else {

						Toast.makeText(context, "Error", Toast.LENGTH_LONG)
								.show();
					}

					AdminRequestAdapter adapter = new AdminRequestAdapter(
							context, adminRequests,
							new AdminRequestAdapter.ListSelected() {

								@Override
								public void onListSelected(
										ArrayList<String> selectedgroupids,
										ArrayList<String> userids) {
									// TODO Auto-generated method stub
									mselectedgroupids = selectedgroupids;
									muserids = userids;
								}
							}, 0);

					listview.setAdapter(adapter);

				} catch (Exception exp) {

					Toast.makeText(context, "Error", Toast.LENGTH_LONG).show();
				}
			}
		});

		connectServerImage.setMode(ConnectServerImage.MODE_POST);
		connectServerImage.setNames(names);
		connectServerImage.setValues(values);
		connectServerImage.execute(url);
	}

	ArrayList<String> mselectedgroupids = new ArrayList<String>();
	ArrayList<String> muserids = new ArrayList<String>();

	/**
	 * Update notification
	 * 
	 * @param accepttype
	 *            - accept and reject
	 */
	private void UpdateNotification(String accepttype) {
		String url = StringURLs.ACCEPTGROUPADMINREQUEST;
		ArrayList<String> names = new ArrayList<String>();
		ArrayList<String> values = new ArrayList<String>();

		names.add("userid");
		names.add("group_id");
		names.add("accepttype");

		String sgroupids = "";
		String suserids = "";

		for (int i = 0; i < mselectedgroupids.size(); i++) {

			if (sgroupids.length() == 0) {

				sgroupids = mselectedgroupids.get(i);
				suserids = muserids.get(i);
			} else {

				sgroupids = sgroupids + "," + mselectedgroupids.get(i);
				suserids = suserids + "," + muserids.get(i);
			}
		}

		values.add(suserids);
		values.add(sgroupids);
		values.add(accepttype);

		ConnectServerImage connectServerImage = new ConnectServerImage();
		connectServerImage.setContext(context);
		connectServerImage.setListener(new ConnectServerListener() {

			@Override
			public void onServerResponse(String sJSON, JSONObject jsonObject) {
				// TODO Auto-generated method stub

				try {

					if (sJSON.length() > 0) {

						JSONObject jsonObject2 = new JSONObject(sJSON);

						if (jsonObject2.getJSONObject("response")
								.getString("success").equalsIgnoreCase("1") == true) {

							String msgcode = jsonObject.getJSONObject(
									"response").getString("msgcode");

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											msgcode), Toast.LENGTH_LONG).show();

							GetNotificationCount(1);
						} else {

							String msgcode = jsonObject.getJSONObject(
									"response").getString("msgcode");

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											msgcode), Toast.LENGTH_LONG).show();
						}

					} else {

						Toast.makeText(context,
								Main.getStringResourceByName(context, "c100"),
								Toast.LENGTH_LONG).show();
					}
				} catch (Exception exp) {

					Toast.makeText(context,
							Main.getStringResourceByName(context, "c100"),
							Toast.LENGTH_LONG).show();
				}
			}
		});

		connectServerImage.setMode(ConnectServerImage.MODE_POST);
		connectServerImage.setNames(names);
		connectServerImage.setValues(values);
		connectServerImage.execute(url);
	}

	/**
	 * Get member request notification list
	 */
	private void GetMemberRequestList() {

		GetNotificationCount(0);

		String url = StringURLs.MEMBERREQUEST;

		ArrayList<String> names = new ArrayList<String>();
		ArrayList<String> values = new ArrayList<String>();

		names.add("user_id");

		LocalData data = new LocalData(context);

		values.add(data.GetS("userid"));

		ConnectServerImage connectServerImage = new ConnectServerImage();
		connectServerImage.setContext(context);
		;
		connectServerImage.setListener(new ConnectServerListener() {

			@Override
			public void onServerResponse(String sJSON, JSONObject jsonObject) {
				// TODO Auto-generated method stub

				try {

					adminRequests = new ArrayList<NotificationFragment.GetAdminRequest>();

					if (sJSON.length() > 0) {

						JSONObject jsonObject2 = new JSONObject(sJSON);

						if (jsonObject2.getJSONObject("response")
								.getString("success").equalsIgnoreCase("1") == true) {

							JSONArray getadminrequest = jsonObject2
									.getJSONArray("getmemberrequest");

							for (int i = 0; i < getadminrequest.length(); i++) {

								GetAdminRequest adminRequest = new GetAdminRequest();

								JSONObject object = getadminrequest
										.getJSONObject(i);

								adminRequest.setGroupid(object
										.getString("group_id"));
								// adminRequest.setPicture(object.getString("profilepicture"));
								adminRequest.setGroupimage(object
										.getString("groupimage"));
								adminRequest.setGroupname(object
										.getString("groupname"));
								adminRequest.setInvitetype(object
										.getString("invitetype"));
								adminRequest.setUserid(object
										.getString("createdby"));
								adminRequest.setUsername(object
										.getString("name"));

								adminRequests.add(adminRequest);
							}

						} else {

							String msgcode = jsonObject.getJSONObject(
									"response").getString("msgcode");

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											msgcode), Toast.LENGTH_LONG).show();
						}

					} else {

						Toast.makeText(context,
								Main.getStringResourceByName(context, "c100"),
								Toast.LENGTH_LONG).show();
					}

					AdminRequestAdapter adapter = new AdminRequestAdapter(
							context, adminRequests,
							new AdminRequestAdapter.ListSelected() {

								@Override
								public void onListSelected(
										ArrayList<String> selectedgroupids,
										ArrayList<String> userids) {
									// TODO Auto-generated method stub
									mselectedgroupids = selectedgroupids;
									muserids = userids;
								}
							}, 1);

					listview.setAdapter(adapter);

				} catch (Exception exp) {

					Toast.makeText(context,
							Main.getStringResourceByName(context, "c100"),
							Toast.LENGTH_LONG).show();
				}
			}
		});

		connectServerImage.setMode(ConnectServerImage.MODE_POST);
		connectServerImage.setNames(names);
		connectServerImage.setValues(values);
		connectServerImage.execute(url);
	}

	/**
	 * 
	 * @author karthik
	 *
	 */
	public class GetAdminRequest {
		private String userid = "";
		private String picture = "";
		private String username = "";
		private String groupname = "";
		private String groupid = "";
		private String invitetype = "";
		private String groupimage = "";

		/**
		 * @return the userid
		 */
		public String getUserid() {
			return userid;
		}

		/**
		 * @param userid
		 *            the userid to set
		 */
		public void setUserid(String userid) {
			this.userid = userid;
		}

		/**
		 * @return the picture
		 */
		public String getPicture() {
			return picture;
		}

		/**
		 * @param picture
		 *            the picture to set
		 */
		public void setPicture(String picture) {
			this.picture = picture;
		}

		/**
		 * @return the username
		 */
		public String getUsername() {
			return username;
		}

		/**
		 * @param username
		 *            the username to set
		 */
		public void setUsername(String username) {
			this.username = username;
		}

		/**
		 * @return the groupname
		 */
		public String getGroupname() {
			return groupname;
		}

		/**
		 * @param groupname
		 *            the groupname to set
		 */
		public void setGroupname(String groupname) {
			this.groupname = groupname;
		}

		/**
		 * @return the groupid
		 */
		public String getGroupid() {
			return groupid;
		}

		/**
		 * @param groupid
		 *            the groupid to set
		 */
		public void setGroupid(String groupid) {
			this.groupid = groupid;
		}

		/**
		 * @return the invitetype
		 */
		public String getInvitetype() {
			return invitetype;
		}

		/**
		 * @param invitetype
		 *            the invitetype to set
		 */
		public void setInvitetype(String invitetype) {
			this.invitetype = invitetype;
		}

		/**
		 * @return the groupimage
		 */
		public String getGroupimage() {
			return groupimage;
		}

		/**
		 * @param groupimage
		 *            the groupimage to set
		 */
		public void setGroupimage(String groupimage) {
			this.groupimage = groupimage;
		}
	}
}