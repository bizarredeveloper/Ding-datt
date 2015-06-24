package com.bizarre.dingdatt;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.URL;
import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONObject;

import com.bizarre.dingdatt.adapter.GroupMemberAdapter;
import com.bizarre.dingdatt.strings.StringURLs;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Environment;
import android.provider.MediaStore.Images;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ListView;
import android.widget.ProgressBar;
import android.widget.ShareActionProvider;
import android.widget.TextView;
import android.widget.Toast;

/**
 * A placeholder fragment containing a simple view.
 */
public class MemberListFragment extends Fragment {

	private FragmentActivity context;
	private View rootView;
	LayoutInflater inflater;
	ListView listView;
	String group_id = "";
	String group_name = "";
	ArrayList<GroupMembers> groupMembers = new ArrayList<GroupMembers>();
	GroupMembers pojo = new GroupMembers();
	String tag = "";
	ConnectServerSearch connectServerSearch;
	TextView error;
	ProgressBar progressBar;
	int owner = 0;

	public MemberListFragment(FragmentActivity context, String group_id,
			int owner) {

		this.context = context;
		this.group_id = group_id;
		this.owner = owner;
	}

	ShareActionProvider shareActionProvider;

	/**
	 * 
	 * @param shareActionProvider
	 */
	public void getShareIntent(ShareActionProvider shareActionProvider) {

		this.shareActionProvider = shareActionProvider;

		String url = StringURLs.GETGROUPDETAILS;

		ArrayList<String> names = new ArrayList<String>();
		ArrayList<String> values = new ArrayList<String>();

		names.add("group_id");
		names.add("timezone");

		values.add(group_id);
		values.add(Main.GetTimeZone());

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

							if (jsonObject2.getJSONArray("Groupdetails")
									.getJSONObject(0).getString("groupimage")
									.length() != 0) {
								group_name = jsonObject2
										.getJSONArray("Groupdetails")
										.getJSONObject(0)
										.getString("groupname");
								new Share().execute(jsonObject2
										.getJSONArray("Groupdetails")
										.getJSONObject(0)
										.getString("groupimage"));
							} else {
								// String msgcode =
								// jsonObject.getJSONObject("response").getString("msgcode");

								// Toast.makeText(context,
								// Main.getStringResourceByName(context,
								// msgcode), Toast.LENGTH_LONG).show();
							}

						} else {

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();
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
	 * 
	 * @author karthik
	 *
	 */
	private class Share extends AsyncTask<String, File, File> {

		ProgressDialog dialog;

		@Override
		protected void onPreExecute() {
			// TODO Auto-generated method stub
			super.onPreExecute();
			dialog = ProgressDialog.show(context, "",
					context.getString(R.string.processing_please_wait));
		}

		@Override
		protected void onPostExecute(File result) {
			// TODO Auto-generated method stub
			super.onPostExecute(result);

			try {
				Intent intent = new Intent(Intent.ACTION_SEND);
				intent.putExtra(
						Intent.EXTRA_TEXT,
						"Dingdatt \nGroup name:"
								+ group_name
								+ " \nLocation: http://projects.bizarresoftware.in/dingdatt/webpage/viewgroupmember/"
								+ group_id + "");

				String path = Images.Media.insertImage(
						context.getContentResolver(), result + "/myImage.png",
						"", null);
				Uri screenshotUri = Uri.parse(path);

				intent.putExtra(Intent.EXTRA_STREAM, screenshotUri);
				intent.setType("image/*");

				if (shareActionProvider != null) {
					shareActionProvider.setShareIntent(intent);
				}
			} catch (Exception exp) {

			}

			dialog.dismiss();
		}

		@Override
		protected File doInBackground(String... params) {
			// TODO Auto-generated method stub
			File storagePath = null;
			try {

				URL url = new URL(params[0]);
				InputStream input = url.openStream();
				try {
					storagePath = Environment.getExternalStorageDirectory();

					OutputStream output = new FileOutputStream(storagePath
							+ "/myImage.png");
					try {
						byte[] buffer = new byte[5120];
						int bytesRead = 0;
						while ((bytesRead = input
								.read(buffer, 0, buffer.length)) >= 0) {
							output.write(buffer, 0, bytesRead);
						}
					} finally {
						output.close();
					}
				} finally {
					input.close();
				}

			} catch (Exception exp) {
				Log.d("Error", exp.getMessage());
			}

			return storagePath;
		}

	}

	/**
	 * on search or filter group member
	 * 
	 * @param text
	 */
	public void onSearchGroupMember(String text) {

		if (text.length() > 0) {

			String url = StringURLs.SEARCHMEMBER;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("group_id");
			names.add("timezone");
			names.add("searchkey");

			values.add(group_id);
			values.add(Main.GetTimeZone());
			values.add(text);

			if (connectServerSearch != null)
				connectServerSearch.cancel(true);

			connectServerSearch = new ConnectServerSearch();

			connectServerSearch.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {

						groupMembers = new ArrayList<GroupMembers>();

						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);

							if (jsonObject2.getJSONObject("response")
									.getString("success").equalsIgnoreCase("1") == true) {

								JSONArray grouplist = jsonObject2
										.getJSONArray("userdetails");

								for (int i = 0; i < grouplist.length(); i++) {

									JSONObject object = grouplist
											.getJSONObject(i);

									GroupMembers groupMember = new GroupMembers();

									// groupMember.setAdmin_user(object.getString("groupadmin_userid"));
									groupMember.setName(object
											.getString("name"));
									groupMember.setPicture(object
											.getString("profilepicture"));
									groupMember.setUser_id(object
											.getString("ID"));

									groupMembers.add(groupMember);
								}

							} else {

							}

						} else {

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();
						}

						GroupMemberAdapter groupAdapter = new GroupMemberAdapter(
								context, groupMembers,
								new GroupMemberAdapter.ClickButtonFromMember() {

									@Override
									public void onClickButtonFromMember(
											String tag, String pos) {
										// TODO Auto-generated method stub

										pojo = groupMembers.get(Integer
												.parseInt(pos));

										if (tag.equalsIgnoreCase(GroupMemberAdapter.EXITGROUP) == true
												|| tag.equalsIgnoreCase(GroupMemberAdapter.REMOVE) == true) {
											ExitRemove(tag, pos);

										} else if (tag
												.equalsIgnoreCase(GroupMemberAdapter.ADD_MEMBER) == true) {

											AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
													context);
											alertDialogBuilder
													.setMessage("Are you sure you want to add into this group ?");
											alertDialogBuilder
													.setPositiveButton(
															"Yes",
															new DialogInterface.OnClickListener() {

																@Override
																public void onClick(
																		DialogInterface arg0,
																		int arg1) {

																	String url = StringURLs.ADDMEMBERINTOGROUP;

																	ArrayList<String> names = new ArrayList<String>();
																	ArrayList<String> values = new ArrayList<String>();

																	names.add("userid");
																	names.add("group_id");
																	names.add("timezone");

																	values.add(pojo
																			.getUser_id());
																	values.add(group_id);
																	values.add(Main
																			.GetTimeZone());

																	ConnectServerImage connectServerImage = new ConnectServerImage();
																	connectServerImage
																			.setContext(context);
																	connectServerImage
																			.setListener(new ConnectServerListener() {

																				@Override
																				public void onServerResponse(
																						String sJSON,
																						JSONObject jsonObject) {
																					// TODO
																					// Auto-generated
																					// method
																					// stub

																					try {
																						if (sJSON
																								.length() > 0) {

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
																								GetGroupMember();
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
																	connectServerImage
																			.setNames(names);
																	connectServerImage
																			.setValues(values);
																	connectServerImage
																			.execute(url);
																}
															});
											alertDialogBuilder
													.setNegativeButton(
															"No",
															new DialogInterface.OnClickListener() {

																@Override
																public void onClick(
																		DialogInterface dialog,
																		int which) {

																}
															});

											AlertDialog alertDialog = alertDialogBuilder
													.create();
											alertDialog.show();
										}
									}
								});

						if (owner == 1)
							groupAdapter.setAddMember(1);
						else
							groupAdapter.setAddMember(0);

						listView.setTag("group");
						listView.setAdapter(groupAdapter);

					} catch (Exception exp) {

						Toast.makeText(context,
								Main.getStringResourceByName(context, "c100"),
								Toast.LENGTH_LONG).show();
					}
				}
			});
			connectServerSearch.setNames(names);
			connectServerSearch.setValues(values);
			connectServerSearch.execute(url);
		} else {
			if (connectServerSearch != null)
				connectServerSearch.cancel(true);

			GetGroupMember();
		}
	}

	/**
	 * 
	 * @author karthik
	 *
	 */
	public class ConnectServerSearch extends AsyncTask<String, String, String> {
		String name = "";
		ConnectServerListener listener;
		ArrayList<String> names = new ArrayList<String>();
		ArrayList<String> values = new ArrayList<String>();

		public ArrayList<String> getNames() {
			return names;
		}

		public void setNames(ArrayList<String> names) {
			this.names = names;
		}

		public ArrayList<String> getValues() {
			return values;
		}

		public void setValues(ArrayList<String> values) {
			this.values = values;
		}

		public void setListener(ConnectServerListener listener) {
			this.listener = listener;
		}

		public ConnectServerSearch() {
			// TODO Auto-generated constructor stub
		}

		@Override
		protected void onPreExecute() {
			// TODO Auto-generated method stub
			super.onPreExecute();
			error.setVisibility(View.GONE);
			progressBar.setVisibility(View.VISIBLE);
		}

		@Override
		protected void onPostExecute(String result) {
			// TODO Auto-generated method stub
			super.onPostExecute(result);

			try {
				JSONObject jsonObject = null;

				if (result.length() != 0) {
					jsonObject = new JSONObject(result);
				}

				listener.onServerResponse(result, jsonObject);

			} catch (Exception exp) {
				error.setText("No Data Available.");
				error.setVisibility(View.VISIBLE);
			} finally {
				progressBar.setVisibility(View.GONE);
			}
		}

		@Override
		protected String doInBackground(String... params) {

			String data = "";

			String url = params[0];

			try {
				HttpConnect client = new HttpConnect(url);
				client.connectForMultipart();

				for (int i = 0; i < names.size(); i++) {
					client.addFormPart(names.get(i), values.get(i));
				}

				client.finishMultipart();

				data = client.getResponse();

				Log.d("test", data);
			} catch (Throwable t) {
				t.printStackTrace();
			}

			return data;
		}

		public byte[] convertFileToByteArray(File f) {
			byte[] byteArray = null;
			try {
				InputStream inputStream = new FileInputStream(f);
				ByteArrayOutputStream bos = new ByteArrayOutputStream();
				byte[] b = new byte[1024 * 8];
				int bytesRead = 0;

				while ((bytesRead = inputStream.read(b)) != -1) {
					bos.write(b, 0, bytesRead);
				}

				byteArray = bos.toByteArray();
			} catch (IOException e) {
				e.printStackTrace();
			}
			return byteArray;
		}

		String convertStreamToString(java.io.InputStream is) {
			java.util.Scanner s = new java.util.Scanner(is).useDelimiter("\\A");
			return s.hasNext() ? s.next() : "";
		}
	}

	@Override
	public View onCreateView(final LayoutInflater inflater1,
			ViewGroup container, Bundle savedInstanceState) {
		this.inflater = inflater1;
		rootView = inflater.inflate(R.layout.members_list, container, false);

		Main.SetAdvertisment(rootView);

		listView = (ListView) rootView.findViewById(R.id.members_list);
		error = (TextView) rootView.findViewById(R.id.error);
		progressBar = (ProgressBar) rootView.findViewById(R.id.progressbar);

		listView.setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> parent, View view,
					int position, long id) {
				// TODO Auto-generated method stub
				Intent intent = new Intent(context, MyProfileActivity.class);
				intent.putExtra("userid", parent.getAdapter().getItem(position)
						.toString());
				startActivity(intent);
			}
		});
		GetGroupMember();

		return rootView;
	}

	/**
	 * Get group member list
	 */
	private void GetGroupMember() {

		try {
			String url = StringURLs.GETGROUPMEMBERLIST;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("group_id");
			names.add("timezone");

			values.add(group_id);
			values.add(Main.GetTimeZone());

			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(context);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

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

									groupMembers.add(groupMember);
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

						GroupMemberAdapter groupAdapter = new GroupMemberAdapter(
								context, groupMembers,
								new GroupMemberAdapter.ClickButtonFromMember() {

									@Override
									public void onClickButtonFromMember(
											String tag, String pos) {
										// TODO Auto-generated method stub

										if (tag.equalsIgnoreCase(GroupMemberAdapter.EXITGROUP) == true
												|| tag.equalsIgnoreCase(GroupMemberAdapter.REMOVE) == true) {
											ExitRemove(tag, pos);
										}
									}
								});

						listView.setTag("group");
						listView.setAdapter(groupAdapter);

					} catch (Exception exp) {

						Toast.makeText(context, "Error.", Toast.LENGTH_LONG)
								.show();
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
	 * 
	 * @param tag
	 * @param pos
	 */
	private void ExitRemove(String tag, String pos) {

		try {
			this.tag = tag;

			UnGroup(pos);

		} catch (Exception exp) {

		}
	}

	/**
	 * Remove or Exit from group
	 * 
	 * @param pos
	 */
	private void UnGroup(String pos) {

		try {
			String url = StringURLs.UNGROUP;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("group_id");
			names.add("user_id");

			values.add(group_id);
			values.add(groupMembers.get(Integer.parseInt(pos)).getUser_id());

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
												msgcode), Toast.LENGTH_LONG)
										.show();

								if (tag.equalsIgnoreCase(GroupMemberAdapter.EXITGROUP)) {

									Intent intent = new Intent(context,
											GroupListActivity.class);
									startActivity(intent);

								} else if (tag
										.equalsIgnoreCase(GroupMemberAdapter.REMOVE)) {

									GetGroupMember();
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

		}
	}

	/**
	 * Group member pojo class
	 * 
	 * @author karthik
	 *
	 */
	public static class GroupMembers {
		private String id = "";
		private String user_id = "";
		private String name = "";
		private String picture = "";
		private String admin_user = "";
		private int invite = -1;

		public String getUser_id() {
			return user_id;
		}

		public void setUser_id(String user_id) {
			this.user_id = user_id;
		}

		public String getName() {
			return name;
		}

		public void setName(String name) {
			this.name = name;
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
		 * @return the admin_user
		 */
		public String getAdmin_user() {
			return admin_user;
		}

		/**
		 * @param admin_user
		 *            the admin_user to set
		 */
		public void setAdmin_user(String admin_user) {
			this.admin_user = admin_user;
		}

		public String getId() {
			return id;
		}

		public void setId(String id) {
			this.id = id;
		}

		public int getInvite() {
			return invite;
		}

		public void setInvite(int invite) {
			this.invite = invite;
		}
	}
}