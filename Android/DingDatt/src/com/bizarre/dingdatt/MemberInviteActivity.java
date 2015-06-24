package com.bizarre.dingdatt;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONObject;

import com.bizarre.dingdatt.MemberListFragment.GroupMembers;
import com.bizarre.dingdatt.adapter.MemberInviteAdapter;
import com.bizarre.dingdatt.strings.StringURLs;

import android.app.Activity;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ListView;
import android.widget.Toast;

public class MemberInviteActivity extends Activity {

	ListView listView = null;
	Button invite;
	Button unInvite;
	String group_id = "";
	int contest_id = 0;
	ArrayList<GroupMembers> groupMembers = new ArrayList<GroupMembers>();
	ArrayList<String> sSelectedMembers = new ArrayList<String>();

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_member_invite);

		Main.SetAdvertisment(this);

		listView = (ListView) findViewById(R.id.listview);
		invite = (Button) findViewById(R.id.Invite);
		unInvite = (Button) findViewById(R.id.Uninvite);

		invite.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (sSelectedMembers.size() > 0)
					InviteGroupMember(sSelectedMembers, INVITE);
			}
		});

		unInvite.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (sSelectedMembers.size() > 0)
					InviteGroupMember(sSelectedMembers, UNINVITE);
			}
		});

		Bundle bundle = getIntent().getExtras();

		String group_name = "";

		if (bundle != null) {
			group_id = Integer.toString(bundle.getInt("groupid"));
			contest_id = bundle.getInt("contestid");
			group_name = bundle.getString("groupname");
		}

		getActionBar().setTitle(group_name);

		if (group_id.length() == 0) {
			Toast.makeText(this, "Invalid group.", Toast.LENGTH_LONG).show();
			finish();
		} else {
			GetGroupMember();
		}
	}

	/**
	 * Get group member list from server
	 */
	private void GetGroupMember() {

		try {
			String url = StringURLs.GETGROUPMEMBERLIST;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("group_id");
			names.add("contest_id");
			names.add("timezone");

			values.add(group_id);
			values.add(Integer.toString(contest_id));
			values.add(Main.GetTimeZone());

			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(MemberInviteActivity.this);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {

					try {

						groupMembers = new ArrayList<GroupMembers>();

						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);

							if (jsonObject2.getJSONObject("response")
									.getString("success").equalsIgnoreCase("1") == true) {

								JSONArray grouplist = jsonObject2
										.getJSONArray("groupmemberlist");

								for (int i = 0; i < grouplist.length(); i++) {

									JSONObject object = grouplist
											.getJSONObject(i);

									GroupMembers groupMember = new GroupMembers();

									groupMember.setAdmin_user(object
											.getString("groupadmin_userid"));
									groupMember.setName(object
											.getString("name"));
									groupMember.setPicture(object
											.getString("profilepicture"));
									groupMember.setUser_id(object
											.getString("user_id"));
									groupMember.setId(object
											.getString("groupmemberid"));
									groupMember.setInvite(object
											.getInt("invited"));

									groupMembers.add(groupMember);
								}

							} else {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										MemberInviteActivity.this,
										Main.getStringResourceByName(
												MemberInviteActivity.this,
												msgcode), Toast.LENGTH_LONG)
										.show();
							}

						} else {

							Toast.makeText(
									MemberInviteActivity.this,
									Main.getStringResourceByName(
											MemberInviteActivity.this, "c100"),
									Toast.LENGTH_LONG).show();
						}

						MemberInviteAdapter adapter = new MemberInviteAdapter(
								MemberInviteActivity.this, groupMembers,
								new MemberInviteAdapter.SelectedList() {

									@Override
									public void onSelectedList(
											ArrayList<String> selecttext) {
										// TODO Auto-generated method stub
										sSelectedMembers = selecttext;
									}
								});

						listView.setTag("group");
						listView.setAdapter(adapter);

					} catch (Exception exp) {

						Toast.makeText(MemberInviteActivity.this, "Error.",
								Toast.LENGTH_LONG).show();
					}
				}
			});

			connectServerImage.setMode(ConnectServerImage.MODE_POST);
			connectServerImage.setNames(names);
			connectServerImage.setValues(values);
			connectServerImage.execute(url);

		} catch (Exception exp) {

		}
	}

	int INVITE = 0;
	int UNINVITE = 1;

	/**
	 * Invite and uninvite given group member id
	 * 
	 * @param memberids
	 * @param invitetype
	 */
	private void InviteGroupMember(ArrayList<String> memberids, int invitetype) {

		try {
			// http://192.168.1.52/dingdatt/invitegroupsforcontest?invite_type=All&timezone=Asia/Culcutta&contest_id=10&group_id=

			String sURL = "";

			if (invitetype == INVITE) {

				sURL = StringURLs.INVITEGROUPMEMBERFORCONTEST;

			} else if (invitetype == UNINVITE) {

				sURL = StringURLs.UNINVITEGROUPMEMBERFORCONTEST;
			}

			ArrayList<String> asName = new ArrayList<String>();
			ArrayList<String> asValue = new ArrayList<String>();

			asName.add("contest_id");
			asName.add("groupmemberid");
			asName.add("timezone");
			asName.add("group_id");

			String groups = "";

			if (memberids != null) {
				for (int j = 0; j < memberids.size(); j++) {

					if (j == 0) {
						groups = memberids.get(j);
					} else {
						groups = groups + "," + memberids.get(j);
					}

				}
			}

			asValue.add(Integer.toString(contest_id));
			asValue.add(groups);
			asValue.add(Main.GetTimeZone());
			asValue.add(group_id);

			sURL = StringURLs.getQuery(sURL, asName, asValue);

			ConnectServer connectServer = new ConnectServer();
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setContext(MemberInviteActivity.this);
			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {

						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);
							JSONObject response = jsonObject2
									.getJSONObject("response");

							String success = response.getString("success");
							String message = response.getString("message");

							if (success.equalsIgnoreCase("1") == true) {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										MemberInviteActivity.this,
										Main.getStringResourceByName(
												MemberInviteActivity.this,
												msgcode), Toast.LENGTH_LONG)
										.show();

							} else {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										MemberInviteActivity.this,
										Main.getStringResourceByName(
												MemberInviteActivity.this,
												msgcode), Toast.LENGTH_LONG)
										.show();
							}

							GetGroupMember();

						} else {

							Toast.makeText(
									MemberInviteActivity.this,
									Main.getStringResourceByName(
											MemberInviteActivity.this, "c100"),
									Toast.LENGTH_LONG).show();
						}

					} catch (Exception exp) {

						Toast.makeText(
								MemberInviteActivity.this,
								Main.getStringResourceByName(
										MemberInviteActivity.this, "c100"),
								Toast.LENGTH_LONG).show();
					}

				}
			});

			connectServer.execute(sURL);

		} catch (Exception exp) {

		}
	}
}
