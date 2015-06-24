package com.bizarre.dingdatt;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.Executor;

import org.apache.http.NameValuePair;
import org.json.JSONArray;
import org.json.JSONObject;

import com.bizarre.dingdatt.MyProfileFragment.GroupPojo;
import com.bizarre.dingdatt.adapter.GroupAdapter;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;
import com.facebook.model.CreateGraphObject;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ListView;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

/**
 * A placeholder fragment containing a simple view.
 */
public class GroupListFragment extends Fragment {

	private FragmentActivity context;
	private View rootView;
	LayoutInflater inflater;
	TextView error;
	ProgressBar progressBar;
	ListView listView;
	ArrayList<MyProfileFragment.GroupPojo> groupPojos = new ArrayList<MyProfileFragment.GroupPojo>();
	ConnectServerSearch connectServerSearch;
	MyProfileFragment.GroupPojo pojo = new GroupPojo();

	public GroupListFragment(final FragmentActivity context) {

		this.context = context;
	}

	int search = 0;

	/**
	 * Get groups by filter text.
	 * 
	 * @param text
	 */
	public void onGetGroupsByFilterText(String text) {

		if (text.length() > 0) {
			// Toast.makeText(context, "test " + text,
			// Toast.LENGTH_LONG).show();
			String url = StringURLs.GETGROUPLISTSEARCH;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("user_id");
			names.add("timezone");
			names.add("searchkey");

			LocalData data = new LocalData(context);

			values.add(data.GetS("userid"));
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

						groupPojos = new ArrayList<MyProfileFragment.GroupPojo>();
						search = 1;

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

									groupPojo.setId(object.getString("ID"));
									groupPojo.setName(object
											.getString("groupname"));
									groupPojo.setPicture(object
											.getString("groupimage"));
									groupPojo.setType(object
											.getString("grouptype"));
									groupPojo.setUserid(object
											.getString("createdby"));
									groupPojo.setUsername(object
											.getString("name"));
									groupPojo.setMember(object.getInt("member"));

									groupPojos.add(groupPojo);
								}

							}

						} else {

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();
						}

						GroupAdapter groupAdapter = new GroupAdapter(context,
								groupPojos, 1, new GroupAdapter.JoinClick() {

									@Override
									public void onJoinClick(String tag,
											String pos) {
										// TODO Auto-generated method stub
										OnSelectGroup(tag, pos);
									}
								});
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
			;
			connectServerSearch.execute(url);
		} else {

			if (connectServerSearch != null)
				connectServerSearch.cancel(true);

			GetGroupList();
		}
	}

	@Override
	public View onCreateView(final LayoutInflater inflater1,
			ViewGroup container, Bundle savedInstanceState) {
		this.inflater = inflater1;
		rootView = inflater.inflate(R.layout.group_list_fragment, container,
				false);

		Main.SetAdvertisment(rootView);

		listView = (ListView) rootView.findViewById(R.id.listview);
		error = (TextView) rootView.findViewById(R.id.error);
		progressBar = (ProgressBar) rootView.findViewById(R.id.progressbar);

		listView.setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> parent, View view,
					final int position, long id) {
				// TODO Auto-generated method stub

				/* if(search == 0) { */
				Intent intent = new Intent(context, MemberListActivity.class);

				LocalData data = new LocalData(context);

				if (data.GetS("userid").equalsIgnoreCase(
						groupPojos.get(position).getUserid()) == true) {
					intent.putExtra("owner", true);
				} else {
					intent.putExtra("owner", false);
				}

				intent.putExtra("groupid", groupPojos.get(position).getId());
				intent.putExtra("groupname", groupPojos.get(position).getName());
				startActivity(intent);
				/* } */
			}
		});

		GetGroupList();

		return rootView;
	}

	/**
	 * On selecting a group in list
	 * 
	 * @param tag
	 *            - selected type
	 * @param pos
	 *            - selected list
	 */
	private void OnSelectGroup(String tag, String pos) {

		try {

			pojo = groupPojos.get(Integer.parseInt(pos));
			String id = groupPojos.get(Integer.parseInt(pos)).getId();

			if (tag.equalsIgnoreCase(GroupAdapter.EDIT_GROUP) == true) {

				Intent intent = new Intent(context,
						CreateEditGroupActivity.class);
				intent.putExtra("groupid", id);
				startActivity(intent);

			} else if (tag.equalsIgnoreCase(GroupAdapter.JOIN) == true) {

				AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
						context);
				alertDialogBuilder
						.setMessage("Are you sure you want to join to this group ?");
				alertDialogBuilder.setPositiveButton("Yes",
						new DialogInterface.OnClickListener() {

							@Override
							public void onClick(DialogInterface arg0, int arg1) {

								String url = StringURLs.MEMBEREQUEATTOGROUP;

								ArrayList<String> names = new ArrayList<String>();
								ArrayList<String> values = new ArrayList<String>();

								names.add("userid");
								names.add("group_id");
								names.add("timezone");

								LocalData localData = new LocalData(context);

								values.add(localData.GetS("userid"));
								values.add(pojo.getId());
								values.add(Main.GetTimeZone());

								ConnectServerImage connectServerImage = new ConnectServerImage();
								connectServerImage.setContext(context);
								connectServerImage
										.setListener(new ConnectServerListener() {

											@Override
											public void onServerResponse(
													String sJSON,
													JSONObject jsonObject) {
												// TODO Auto-generated method
												// stub

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

				alertDialogBuilder.setNegativeButton("No",
						new DialogInterface.OnClickListener() {

							@Override
							public void onClick(DialogInterface dialog,
									int which) {

							}
						});

				AlertDialog alertDialog = alertDialogBuilder.create();
				alertDialog.show();
			} else if (tag.equalsIgnoreCase(GroupAdapter.MEMBER) == true) {
				Intent intent = new Intent(context, MemberListActivity.class);
				intent.putExtra("owner", false);
				intent.putExtra("groupid", id);
				intent.putExtra("groupname", pojo.getName());
				startActivity(intent);
			}

		} catch (Exception exp) {

		}
	}

	/**
	 * Get group list
	 */
	private void GetGroupList() {

		try {
			String url = StringURLs.GETGROUPLIST;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("user_id");
			names.add("timezone");

			LocalData data = new LocalData(context);

			values.add(data.GetS("userid"));
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
								groupPojos, 1, new GroupAdapter.JoinClick() {

									@Override
									public void onJoinClick(String tag,
											String pos) {
										// TODO Auto-generated method stub
										OnSelectGroup(tag, pos);
									}
								});
						listView.setTag("group");
						listView.setAdapter(groupAdapter);

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
	 * Call ConnectServerSearch when search text in search box.
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
}