package com.bizarre.dingdatt;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONObject;

import com.bizarre.dingdatt.MyProfileFragment.GroupPojo;
import com.bizarre.dingdatt.imageloader.ImageLoader;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;

import android.app.Activity;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.net.Uri;
import android.os.Bundle;
import android.provider.MediaStore;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

/**
 * A placeholder fragment containing a simple view.
 */
public class CreateEditGroupFragment extends Fragment {

	private FragmentActivity context;
	private View rootView;
	LayoutInflater inflater;

	EditText group_name;
	TextView group_image;
	ImageView group_image1;
	ImageView group_image2;
	Spinner group_type;
	ImageView group_type1;
	CheckBox followers;
	CheckBox following;
	Button update;

	LinearLayout invite_layout;

	private static int IMAGE = 151;

	String file_name = "";
	String group_id = "";

	private int CREATE = 0;
	private int UPDATE = 1;
	int type = CREATE;

	public CreateEditGroupFragment(FragmentActivity context, int type,
			String group_id) {

		this.context = context;
		this.type = type;
		this.group_id = group_id;
	}

	@Override
	public View onCreateView(final LayoutInflater inflater1,
			ViewGroup container, Bundle savedInstanceState) {
		this.inflater = inflater1;
		rootView = inflater.inflate(R.layout.create_edit_group_fragment,
				container, false);

		Main.SetAdvertisment(rootView);

		group_name = (EditText) rootView.findViewById(R.id.group_name);
		group_image = (TextView) rootView.findViewById(R.id.group_image);
		group_image1 = (ImageView) rootView.findViewById(R.id.group_image1);
		group_image2 = (ImageView) rootView.findViewById(R.id.group_image2);
		group_type = (Spinner) rootView.findViewById(R.id.group_type);
		group_type1 = (ImageView) rootView.findViewById(R.id.group_type1);
		followers = (CheckBox) rootView.findViewById(R.id.Followers);
		following = (CheckBox) rootView.findViewById(R.id.Following);
		update = (Button) rootView.findViewById(R.id.update);
		invite_layout = (LinearLayout) rootView.findViewById(R.id.invite);

		if (type == CREATE) {
			update.setText("Create");
			update.setTextColor(Color.parseColor("#ffffff"));
		} else if (type == UPDATE) {
			update.setText("Update");
			update.setTextColor(Color.parseColor("#ffffff"));
		}

		group_image.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				Intent galleryIntent = new Intent(
						Intent.ACTION_PICK,
						android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
				startActivityForResult(galleryIntent, IMAGE);
			}
		});

		group_image1.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				Intent galleryIntent = new Intent(
						Intent.ACTION_PICK,
						android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
				startActivityForResult(galleryIntent, IMAGE);
			}
		});

		update.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (Validate() == true) {
					if (type == CREATE) {
						UpdateGroup();

					} else {
						UpdateGroup();

					}
				}
			}
		});

		ArrayList<String> group_type_array = new ArrayList<String>();
		group_type_array.add("Open");
		group_type_array.add("Private");

		ArrayAdapter<String> adapter = new ArrayAdapter<String>(context,
				R.layout.spinner_item_view, group_type_array);
		group_type.setAdapter(adapter);
		group_type1.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				group_type.performClick();
			}
		});

		invite_layout.setVisibility(View.GONE);

		group_type.setOnItemSelectedListener(new OnItemSelectedListener() {

			@Override
			public void onItemSelected(AdapterView<?> parent, View view,
					int position, long id) {
				// TODO Auto-generated method stub

				if (parent.getAdapter().getItem(position).toString()
						.equalsIgnoreCase("Open") == true) {

					invite_layout.setVisibility(View.GONE);

				} else if (parent.getAdapter().getItem(position).toString()
						.equalsIgnoreCase("Private") == true) {

					invite_layout.setVisibility(View.VISIBLE);
				}

			}

			@Override
			public void onNothingSelected(AdapterView<?> parent) {
				// TODO Auto-generated method stub

			}
		});

		if (type == UPDATE) {
			GetGroupDetails();

			group_type.setFocusable(false);
			group_type.setClickable(false);

			group_type1.setFocusable(false);
			group_type1.setClickable(false);

			followers.setFocusable(false);
			followers.setClickable(false);

			following.setFocusable(false);
			following.setClickable(false);
		}

		return rootView;
	}

	/**
	 * Get group details
	 */
	private void GetGroupDetails() {

		try {
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

						ArrayList<GroupPojo> groupPojos = new ArrayList<MyProfileFragment.GroupPojo>();

						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);

							if (jsonObject2.getJSONObject("response")
									.getString("success").equalsIgnoreCase("1") == true) {

								JSONArray Groupdetails = jsonObject2
										.getJSONArray("Groupdetails");

								for (int i = 0; i < Groupdetails.length(); i++) {

									JSONObject jsonObject3 = Groupdetails
											.getJSONObject(i);

									GroupPojo groupPojo = new GroupPojo();

									groupPojo.setId(jsonObject3.getString("ID"));
									groupPojo.setName(jsonObject3
											.getString("groupname"));
									groupPojo.setType(jsonObject3
											.getString("grouptype"));
									groupPojo.setUserid(jsonObject3
											.getString("createdby"));
									groupPojo.setPicture(jsonObject3
											.getString("groupimage"));

									group_name.setText(jsonObject3
											.getString("groupname"));

									int loader = R.drawable.avator;
									ImageLoader imageLoader = new ImageLoader(
											context);
									imageLoader.DisplayImage(
											jsonObject3.getString("groupimage"),
											loader, group_image2);

									if (jsonObject3.getString("grouptype")
											.equalsIgnoreCase("open") == true) {

										group_type.setSelection(0);

									} else if (jsonObject3.getString(
											"grouptype").equalsIgnoreCase(
											"private") == true) {

										group_type.setSelection(1);
									}

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
	 * Validate fields
	 * 
	 * @return boolean
	 */
	private boolean Validate() {

		try {

			if (group_name.getText().toString().length() == 0) {
				Toast.makeText(context, "Enter valid group name.",
						Toast.LENGTH_LONG).show();
				group_name.requestFocus();
				return false;
			}

		} catch (Exception exp) {
			return false;
		}

		return true;
	}

	/**
	 * Update group
	 */
	private void UpdateGroup() {

		try {

			ArrayList<String> asName = new ArrayList<String>();

			asName.add("groupname");

			ArrayList<String> asValue = new ArrayList<String>();

			LocalData localData = new LocalData(context);

			asValue.add(group_name.getText().toString());

			String sUrl = "";

			if (type == CREATE) {
				sUrl = StringURLs.CREATEGROUP;

				asName.add("userid");
				asName.add("grouptype");

				asValue.add(localData.GetS("userid"));
				asValue.add(group_type.getSelectedItem().toString()
						.toLowerCase());

				if (group_type.getSelectedItem().toString().toLowerCase()
						.equalsIgnoreCase("open") == false) {

					asName.add("following");
					asName.add("follower");

					asValue.add(following.isChecked() == true ? "1" : "0");
					asValue.add(followers.isChecked() == true ? "1" : "0");
				}

			} else {
				sUrl = StringURLs.UPDATEGROUPDETAILS;

				asName.add("group_id");

				asValue.add(group_id);
			}

			ConnectServerImage connectServer = new ConnectServerImage();
			connectServer.setContext(context);
			connectServer.setNames(asName);
			connectServer.setValues(asValue);

			if (file_name.length() != 0)
				connectServer.SendImage("uploadimage", file_name);

			// connectServer.setMode(ConnectServer.MODE_POST);
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

							if (success.equalsIgnoreCase("1")) {

								if (type == CREATE) {
									Toast.makeText(context,
											"Successfully Added.",
											Toast.LENGTH_LONG).show();
								} else {
									Toast.makeText(context,
											"Successfully Updated.",
											Toast.LENGTH_LONG).show();
								}

								Intent intent = new Intent(context,
										GroupListActivity.class);
								startActivity(intent);
								context.finish();

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

			connectServer.execute(sUrl);

		} catch (Exception exp) {

		}
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		// TODO Auto-generated method stub
		super.onActivityResult(requestCode, resultCode, data);

		if (requestCode == IMAGE && null != data) {
			// Get the Image from data

			if (resultCode == Activity.RESULT_OK) {
				Uri selectedImage = data.getData();
				String[] filePathColumn = { MediaStore.Images.Media.DATA };

				// Get the cursor
				Cursor cursor = context.getContentResolver().query(
						selectedImage, filePathColumn, null, null, null);
				// Move to first row
				cursor.moveToFirst();

				int columnIndex = cursor.getColumnIndex(filePathColumn[0]);
				String imgDecodableString = cursor.getString(columnIndex);
				String[] filenames = imgDecodableString.split("/");
				String filename = filenames[filenames.length - 1];
				cursor.close();
				// Set the Image in ImageView after decoding the String
				group_image.setText(filename);

				file_name = imgDecodableString;

				BitmapFactory.Options options = new BitmapFactory.Options();
				options.inPreferredConfig = Bitmap.Config.ARGB_8888;
				Bitmap bitmap = BitmapFactory.decodeFile(file_name, options);
				group_image2.setImageBitmap(bitmap);
			} else {
				group_image.setText("");
				file_name = "";
				Toast.makeText(context, "You haven't picked Image",
						Toast.LENGTH_LONG).show();
			}

		}
	}
}