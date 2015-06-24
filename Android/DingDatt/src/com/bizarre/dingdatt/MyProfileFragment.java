package com.bizarre.dingdatt;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONObject;

import com.bizarre.dingdatt.adapter.FollowAdapter;
import com.bizarre.dingdatt.adapter.GalleryAdapter;
import com.bizarre.dingdatt.adapter.GroupAdapter;
import com.bizarre.dingdatt.imageloader.ImageLoader;
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
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

public class MyProfileFragment extends Fragment {

	private FragmentActivity context;
	private View rootView;
	LayoutInflater inflater;

	GridView gridview;
	ListView listview;

	TextView history;
	TextView following;
	TextView follower;
	TextView group;

	LinearLayout history_layout;
	LinearLayout following_layout;
	LinearLayout follower_layout;
	LinearLayout group_layout;

	TextView txtFollowing;
	TextView txtFollowers;
	TextView txtWon;
	TextView txtParticipated;

	TextView name;
	TextView header;
	ImageView profile;

	Button follow;
	int followtype = 0;
	int unfollow = 1;

	String user_id = "";

	ArrayList<Integer> images = new ArrayList<Integer>();
	ArrayList<String> followers = new ArrayList<String>();
	ArrayList<MyProfileFragment.GroupPojo> groupPojos = new ArrayList<MyProfileFragment.GroupPojo>();
	ArrayList<MyProfileFragment.MyHistory> myHistories = new ArrayList<MyProfileFragment.MyHistory>();

	public MyProfileFragment(FragmentActivity context, String user_id) {

		this.context = context;
		this.user_id = user_id;

		LocalData data = new LocalData(context);

		if (data.GetS("userid").equalsIgnoreCase(this.user_id) == true) {
			this.user_id = "";
		}
	}

	@Override
	public View onCreateView(final LayoutInflater inflater1,
			ViewGroup container, Bundle savedInstanceState) {
		this.inflater = inflater1;
		rootView = inflater.inflate(R.layout.my_profile, container, false);

		Main.SetAdvertisment(rootView);

		gridview = (GridView) rootView.findViewById(R.id.image);
		listview = (ListView) rootView.findViewById(R.id.list_view);

		history = (TextView) rootView.findViewById(R.id.history);
		following = (TextView) rootView.findViewById(R.id.Following);
		follower = (TextView) rootView.findViewById(R.id.Followers);
		group = (TextView) rootView.findViewById(R.id.Group);

		history_layout = (LinearLayout) rootView
				.findViewById(R.id.history_layout);
		following_layout = (LinearLayout) rootView
				.findViewById(R.id.Following_layout);
		follower_layout = (LinearLayout) rootView
				.findViewById(R.id.Followers_layout);
		group_layout = (LinearLayout) rootView.findViewById(R.id.Group_layout);

		ChangeBackgroundColor(history_layout);

		txtFollowers = (TextView) rootView.findViewById(R.id.txtFollowers);
		txtFollowing = (TextView) rootView.findViewById(R.id.txtfollowing);
		txtParticipated = (TextView) rootView
				.findViewById(R.id.txtParticipated);
		txtWon = (TextView) rootView.findViewById(R.id.txtWon);

		name = (TextView) rootView.findViewById(R.id.name);
		profile = (ImageView) rootView.findViewById(R.id.profile);
		header = (TextView) rootView.findViewById(R.id.header);

		follow = (Button) rootView.findViewById(R.id.follow);

		follow.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				GetFollowerList();

			}
		});

		if (user_id.length() == 0) {

			header.setText(getString(R.string.my_profile));
		} else {

			header.setText(getString(R.string.view_profile));
		}

		gridview.setVisibility(View.VISIBLE);
		listview.setVisibility(View.GONE);

		gridview.setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> parent, View view,
					int position, long id) {
				// TODO Auto-generated method stub

				Intent intent = new Intent(context,
						ContestInfoSampleActivity.class);
				intent.putExtra("contest_id", myHistories.get(position)
						.getContest_id());
				startActivity(intent);
			}
		});

		listview.setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> parent, View view,
					int position, long id) {
				// TODO Auto-generated method stub

				if (listview.getTag().toString().equalsIgnoreCase("follow") == true) {
					Intent intent = new Intent(context, MyProfileActivity.class);
					intent.putExtra("userid",
							parent.getAdapter().getItem(position).toString());
					startActivity(intent);
				} else if (listview.getTag().toString()
						.equalsIgnoreCase("group") == true) {
					LocalData data = new LocalData(context);

					if (groupPojos.get(position).getUserid()
							.equalsIgnoreCase(data.GetS("userid")) == true) {

						Intent intent = new Intent(context,
								CreateEditGroupActivity.class);
						intent.putExtra("groupid", groupPojos.get(position)
								.getId());
						startActivity(intent);

					} else {

						Intent intent = new Intent(context,
								MemberListActivity.class);

						if (data.GetS("userid").equalsIgnoreCase(
								groupPojos.get(position).getUserid()) == true) {
							intent.putExtra("owner", true);
						} else {
							intent.putExtra("owner", false);
						}

						intent.putExtra("groupid", groupPojos.get(position)
								.getId());
						intent.putExtra("groupname", groupPojos.get(position)
								.getName());
						startActivity(intent);/*
											 * Intent intent = new
											 * Intent(context,
											 * MemberListActivity.class);
											 * intent.putExtra("groupid",
											 * groupPojos
											 * .get(position).getId());
											 * startActivity(intent);
											 */
					}
				}
			}
		});

		history.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				ChangeBackgroundColor(history_layout);

				gridview.setVisibility(View.VISIBLE);
				listview.setVisibility(View.GONE);

				GetUserHistory();
			}
		});

		following.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				ChangeBackgroundColor(following_layout);

				gridview.setVisibility(View.GONE);
				listview.setVisibility(View.VISIBLE);

				followtype = 0;

				if (user_id.length() > 0) {
					unfollow = 0;
				} else {
					unfollow = 1;
				}

				GetFollowingList();
			}
		});

		follower.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				ChangeBackgroundColor(follower_layout);

				gridview.setVisibility(View.GONE);
				listview.setVisibility(View.VISIBLE);

				followtype = 1;
				unfollow = 0;
				GetFollowingList();
			}
		});

		group.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				ChangeBackgroundColor(group_layout);

				gridview.setVisibility(View.GONE);
				listview.setVisibility(View.VISIBLE);

				GetGroup();
			}
		});

		GetUserProfileDetails();
		GetUserHistory();

		return rootView;
	}

	/**
	 * Change background color for tab view
	 * 
	 * @param view
	 */
	private void ChangeBackgroundColor(View view) {
		history_layout.setBackgroundColor(Color.parseColor("#AFFF84"));
		following_layout.setBackgroundColor(Color.parseColor("#AFFF84"));
		follower_layout.setBackgroundColor(Color.parseColor("#AFFF84"));
		group_layout.setBackgroundColor(Color.parseColor("#AFFF84"));

		view.setBackgroundColor(Color.parseColor("#8EE8DE"));
	}

	/**
	 * Get user history
	 */
	private void GetUserHistory() {
		try {

			String url = StringURLs.MYHISTORY;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("userid");
			names.add("timezone");

			LocalData data = new LocalData(context);

			if (user_id.length() == 0)
				values.add(data.GetS("userid"));
			else
				values.add(user_id);

			// values.add(data.GetS("userid"));

			values.add(Main.GetTimeZone());

			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(context);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {

						myHistories = new ArrayList<MyProfileFragment.MyHistory>();

						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);

							if (jsonObject2.getJSONObject("response")
									.getString("success").equalsIgnoreCase("1") == true) {

								JSONArray myhistory = jsonObject2
										.getJSONArray("myhistory");

								for (int i = 0; i < myhistory.length(); i++) {

									JSONObject jsonObject3 = myhistory
											.getJSONObject(i);

									MyProfileFragment.MyHistory history = new MyProfileFragment.MyHistory();

									history.setContest_id(jsonObject3
											.getInt("contest_id"));
									history.setDropbox_path(jsonObject3
											.getString("dropbox_path"));
									// history.setId(jsonObject3.getInt("ID"));
									history.setContest_name(jsonObject3
											.getString("contest_name"));
									history.setUploaddate(jsonObject3
											.getString("uploaddate"));
									history.setUploadtopic(jsonObject3
											.getString("uploadtopic"));
									history.setContesttype(jsonObject3
											.getString("contesttype"));
									// history.setUser_id(jsonObject3.getInt("user_id"));
									history.setUploadfile(jsonObject3
											.getString("uploadfile"));

									myHistories.add(history);
								}

								GalleryAdapter adapter = new GalleryAdapter(
										context, myHistories);
								gridview.setAdapter(adapter);

							} else {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();
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

		}
	}

	/**
	 * Get group list from server
	 */
	private void GetGroup() {

		try {
			String url = StringURLs.GETGROUPLIST;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("user_id");
			names.add("timezone");

			LocalData data = new LocalData(context);

			if (user_id.length() == 0) {

				values.add(data.GetS("userid"));
			} else {

				values.add(user_id);
			}

			values.add(Main.GetTimeZone());

			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(context);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {

						groupPojos = new ArrayList<MyProfileFragment.GroupPojo>();

						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);

							if (jsonObject2.getJSONObject("response")
									.getString("success").equalsIgnoreCase("1") == true) {

								JSONArray grouplist = jsonObject2
										.getJSONArray("grouplist");

								for (int i = 0; i < grouplist.length(); i++) {

									JSONObject object = grouplist
											.getJSONObject(i);

									GroupPojo groupPojo = new GroupPojo();

									groupPojo.setId(object
											.getString("group_id"));
									groupPojo.setName(object
											.getString("groupname"));
									groupPojo.setPicture(object
											.getString("groupimage"));
									groupPojo.setType(object
											.getString("grouptype"));
									groupPojo.setUserid(object
											.getString("createdby"));
									groupPojo.setUsername(object
											.getString("username"));

									groupPojos.add(groupPojo);
								}

							} else {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();
							}

						} else {

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();
						}

						GroupAdapter groupAdapter = new GroupAdapter(context,
								groupPojos);
						listview.setTag("group");
						listview.setAdapter(groupAdapter);

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
	 * Get user profile details.
	 */
	private void GetUserProfileDetails() {

		try {

			String url = StringURLs.VIEWPROFILE;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("timezone");
			names.add("userid");

			LocalData data = new LocalData(context);
			values.add(Main.GetTimeZone());

			if (user_id.length() == 0)
				values.add(data.GetS("userid"));
			else {
				values.add(user_id);

				names.add("myuserid");
				values.add(data.GetS("userid"));
			}

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

								JSONObject viewmyprofile = jsonObject2
										.getJSONObject("viewmyprofile");

								txtFollowing.setText(viewmyprofile
										.getInt("following") + "");
								txtFollowers.setText(viewmyprofile
										.getInt("followers") + "");
								txtParticipated.setText(viewmyprofile
										.getInt("participated") + "");
								txtWon.setText(viewmyprofile.getInt("won") + "");

								name.setText(viewmyprofile.getString("name"));

								int loader = R.drawable.avator;
								ImageLoader imageLoader = new ImageLoader(
										context);
								imageLoader.DisplayImage(viewmyprofile
										.getString("profilepicture"), loader,
										profile);

								if (user_id.length() != 0) {

									int follow = jsonObject2.getInt("follow");

									if (follow == 1) {
										name.setCompoundDrawablesWithIntrinsicBounds(
												0, 0, R.drawable.bell_symbol, 0);
										MyProfileFragment.this.follow
												.setVisibility(View.GONE);

									} else {

										name.setCompoundDrawablesWithIntrinsicBounds(
												0, 0, 0, 0);
										MyProfileFragment.this.follow
												.setVisibility(View.VISIBLE);
									}
								} else {
									name.setCompoundDrawablesWithIntrinsicBounds(
											0, 0, 0, 0);
									MyProfileFragment.this.follow
											.setVisibility(View.GONE);
								}

							} else {
								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();
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
	 * Get followers list
	 */
	private void GetFollowerList() {

		try {
			String url = StringURLs.FOLLOWER;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("userid");
			names.add("followerid");

			LocalData data = new LocalData(context);

			values.add(user_id);
			values.add(data.GetS("userid"));

			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(context);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {

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
												msgcode), Toast.LENGTH_LONG)
										.show();

								GetUserProfileDetails();

							} else {
								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();
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

		}

	}

	/**
	 * Get following list
	 */
	private void GetFollowingList() {

		try {

			String url = "";

			if (followtype == 0) {
				url = StringURLs.GETFOLLOWINGLIST;

			} else {
				url = StringURLs.GETFOLLOWERLIST;

			}

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("userid");
			names.add("timezone");

			LocalData data = new LocalData(context);

			if (user_id.length() == 0)
				values.add(data.GetS("userid"));
			else
				values.add(user_id);

			values.add(Main.GetTimeZone());

			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(context);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {

						ArrayList<FollowPojo> followPojos = new ArrayList<MyProfileFragment.FollowPojo>();

						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);

							if (jsonObject2.getJSONObject("response")
									.getString("success").equalsIgnoreCase("1") == true) {

								JSONArray followerlist = jsonObject2
										.getJSONArray("followerlist");

								for (int i = 0; i < followerlist.length(); i++) {

									JSONObject followerObject = followerlist
											.getJSONObject(i);

									FollowPojo followPojo = new FollowPojo();

									if (followtype == 0)
										followPojo.setId(followerObject
												.getString("followinguserid"));
									else
										followPojo.setId(followerObject
												.getString("followerid"));

									followPojo.setName(followerObject
											.getString("name"));
									followPojo.setProfilepic(followerObject
											.getString("profilepicture"));

									followPojos.add(followPojo);
								}

							} else {
								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();

							}
						} else {

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();

						}

						FollowAdapter adapter = new FollowAdapter(context,
								followPojos, unfollow,
								new FollowAdapter.ClickUnfollow() {

									@Override
									public void onClickUnfollow(String userid) {
										// TODO Auto-generated method stub
										String url = StringURLs.UNFOLLOW;

										ArrayList<String> names = new ArrayList<String>();
										ArrayList<String> values = new ArrayList<String>();

										names.add("user_id");
										names.add("following_id");

										LocalData localData = new LocalData(
												context);

										values.add(localData.GetS("userid"));
										values.add(userid);

										ConnectServerImage connectServerImage = new ConnectServerImage();
										connectServerImage.setContext(context);
										connectServerImage
												.setListener(new ConnectServerListener() {

													@Override
													public void onServerResponse(
															String sJSON,
															JSONObject jsonObject) {
														// TODO Auto-generated
														// method stub

														try {

															if (sJSON.length() > 0) {

																JSONObject jsonObject2 = new JSONObject(
																		sJSON);

																if (jsonObject2
																		.getJSONObject(
																				"response")
																		.getString(
																				"success")
																		.equalsIgnoreCase(
																				"1") == true) {

																	String msgcode = jsonObject
																			.getJSONObject(
																					"response")
																			.getString(
																					"msgcode");

																	Toast.makeText(
																			context,
																			Main.getStringResourceByName(
																					context,
																					msgcode),
																			Toast.LENGTH_LONG)
																			.show();

																	GetFollowingList();
																	GetUserProfileDetails();

																} else {

																	String msgcode = jsonObject
																			.getJSONObject(
																					"response")
																			.getString(
																					"msgcode");

																	Toast.makeText(
																			context,
																			Main.getStringResourceByName(
																					context,
																					msgcode),
																			Toast.LENGTH_LONG)
																			.show();
																}

															} else {

																Toast.makeText(
																		context,
																		Main.getStringResourceByName(
																				context,
																				"c100"),
																		Toast.LENGTH_LONG)
																		.show();
															}

														} catch (Exception exp) {

															Toast.makeText(
																	context,
																	Main.getStringResourceByName(
																			context,
																			"c100"),
																	Toast.LENGTH_LONG)
																	.show();
														}
													}
												});

										connectServerImage
												.setMode(ConnectServerImage.MODE_POST);
										connectServerImage.setNames(names);
										connectServerImage.setValues(values);
										connectServerImage.execute(url);
									}
								});

						listview.setTag("follow");
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

		} catch (Exception exp) {

		}

	}

	public static class GroupPojo {

		private String id = "";
		private String name = "";
		private String picture = "";
		private String type = "";
		private String userid = "";
		private String username = "";
		private int member = -1;

		public GroupPojo() {

		}

		public String getId() {
			return id;
		}

		public void setId(String id) {
			this.id = id;
		}

		public String getName() {
			return name;
		}

		public void setName(String name) {
			this.name = name;
		}

		public String getPicture() {
			return picture;
		}

		public void setPicture(String picture) {
			this.picture = picture;
		}

		public String getType() {
			return type;
		}

		public void setType(String type) {
			this.type = type;
		}

		public String getUserid() {
			return userid;
		}

		public void setUserid(String userid) {
			this.userid = userid;
		}

		public String getUsername() {
			return username;
		}

		public void setUsername(String username) {
			this.username = username;
		}

		/**
		 * @return the member
		 */
		public int getMember() {
			return member;
		}

		/**
		 * @param member
		 *            the member to set
		 */
		public void setMember(int member) {
			this.member = member;
		}
	}

	public class FollowPojo {

		private String profilepic = "";
		private String id = "";
		private String name = "";

		public String getProfilepic() {
			return profilepic;
		}

		public void setProfilepic(String profilepic) {
			this.profilepic = profilepic;
		}

		public String getId() {
			return id;
		}

		public void setId(String id) {
			this.id = id;
		}

		public String getName() {
			return name;
		}

		public void setName(String name) {
			this.name = name;
		}
	}

	public class MyHistory {

		private int id = -1;
		private int contest_id = -1;
		private String contest_name = "";
		private int user_id = -1;
		private String uploadfile = "";
		private String dropbox_path = "";
		private String uploaddate = "";
		private String uploadtopic = "";
		private String contesttype = "";

		public int getId() {
			return id;
		}

		public void setId(int id) {
			this.id = id;
		}

		public int getContest_id() {
			return contest_id;
		}

		public void setContest_id(int contest_id) {
			this.contest_id = contest_id;
		}

		public int getUser_id() {
			return user_id;
		}

		public void setUser_id(int user_id) {
			this.user_id = user_id;
		}

		public String getUploadfile() {
			return uploadfile;
		}

		public void setUploadfile(String uploadfile) {
			this.uploadfile = uploadfile;
		}

		public String getDropbox_path() {
			return dropbox_path;
		}

		public void setDropbox_path(String dropbox_path) {
			this.dropbox_path = dropbox_path;
		}

		public String getUploaddate() {
			return uploaddate;
		}

		public void setUploaddate(String uploaddate) {
			this.uploaddate = uploaddate;
		}

		public String getUploadtopic() {
			return uploadtopic;
		}

		public void setUploadtopic(String uploadtopic) {
			this.uploadtopic = uploadtopic;
		}

		public String getContesttype() {
			return contesttype;
		}

		public void setContesttype(String contesttype) {
			this.contesttype = contesttype;
		}

		public String getContest_name() {
			return contest_name;
		}

		public void setContest_name(String contest_name) {
			this.contest_name = contest_name;
		}
	}
}