package com.bizarre.dingdatt;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import org.json.JSONArray;
import org.json.JSONObject;

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
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup.LayoutParams;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RadioButton;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

/**
 * A placeholder fragment containing a simple view.
 */
public class EditProfileFragment extends Fragment {

	private FragmentActivity context;
	private View rootView;
	LayoutInflater inflater;

	EditText firstname;
	EditText lastname;
	EditText username;

	ImageView picture;

	TextView uploadimage1;
	ImageView uploadimage2;

	EditText mobile;
	EditText email;
	EditText newpassword;
	EditText confirmpwd;

	EditText facebook;
	EditText twitter;
	EditText instagram;

	TextView dob1;
	ImageView dob2;

	RadioButton male;
	RadioButton female;

	EditText hometown;
	TextView timezone;
	EditText school;

	EditText occupation;
	Spinner marital;
	EditText noofkids;
	LinearLayout noofkidslayout;

	EditText favorite;
	LinearLayout interest;
	Button updateprofile;
	String file_name = "";

	public EditProfileFragment(FragmentActivity context) {

		this.context = context;
	}

	ArrayList<String> maritalname = new ArrayList<String>();
	int maritalpos = 0;
	private static int IMAGE = 10;

	@Override
	public View onCreateView(final LayoutInflater inflater1,
			ViewGroup container, Bundle savedInstanceState) {
		this.inflater = inflater1;
		rootView = inflater.inflate(R.layout.edit_profile, container, false);

		Main.SetAdvertisment(rootView);

		firstname = (EditText) rootView.findViewById(R.id.firstname);
		lastname = (EditText) rootView.findViewById(R.id.lastname);
		;
		username = (EditText) rootView.findViewById(R.id.user_name);
		;

		picture = (ImageView) rootView.findViewById(R.id.picture);
		;

		uploadimage1 = (TextView) rootView.findViewById(R.id.uploadimage1);
		uploadimage2 = (ImageView) rootView.findViewById(R.id.uploadimage2);

		uploadimage1.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				Intent galleryIntent = new Intent(
						Intent.ACTION_PICK,
						android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
				// Start the Intent
				startActivityForResult(galleryIntent, IMAGE);
			}
		});

		uploadimage2.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				Intent galleryIntent = new Intent(
						Intent.ACTION_PICK,
						android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
				// Start the Intent
				startActivityForResult(galleryIntent, IMAGE);
			}
		});

		mobile = (EditText) rootView.findViewById(R.id.mobile);
		;
		email = (EditText) rootView.findViewById(R.id.email);
		;
		newpassword = (EditText) rootView.findViewById(R.id.new_password);
		;
		confirmpwd = (EditText) rootView.findViewById(R.id.confirm_password);
		;

		facebook = (EditText) rootView.findViewById(R.id.facebook);
		;
		twitter = (EditText) rootView.findViewById(R.id.twitter);
		;
		instagram = (EditText) rootView.findViewById(R.id.instagram);
		;

		dob1 = (TextView) rootView.findViewById(R.id.dob1);
		dob2 = (ImageView) rootView.findViewById(R.id.dob2);

		male = (RadioButton) rootView.findViewById(R.id.male);
		female = (RadioButton) rootView.findViewById(R.id.female);

		hometown = (EditText) rootView.findViewById(R.id.hometown);
		timezone = (TextView) rootView.findViewById(R.id.timezone);
		school = (EditText) rootView.findViewById(R.id.school);
		;

		occupation = (EditText) rootView.findViewById(R.id.occupation);
		;
		marital = (Spinner) rootView.findViewById(R.id.marital);
		;
		noofkids = (EditText) rootView.findViewById(R.id.noofkids);
		;
		noofkidslayout = (LinearLayout) rootView
				.findViewById(R.id.noofkidslayout);

		favorite = (EditText) rootView.findViewById(R.id.favorite);
		;
		interest = (LinearLayout) rootView.findViewById(R.id.interest);
		;
		updateprofile = (Button) rootView.findViewById(R.id.updateprofile);
		updateprofile.setTextColor(Color.parseColor("#ffffff"));

		maritalname.add("Single");
		maritalname.add("Married");

		dob1.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				OpenDatePicker();
			}
		});

		dob2.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				OpenDatePicker();
			}
		});

		ArrayAdapter<String> spinneradapter = new ArrayAdapter<String>(context,
				R.layout.spinner_item_view, maritalname);
		marital.setAdapter(spinneradapter);

		marital.setOnItemSelectedListener(new OnItemSelectedListener() {

			@Override
			public void onItemSelected(AdapterView<?> parent, View view,
					int position, long id) {
				// TODO Auto-generated method stub
				maritalpos = position;

				if (position == 0) {

					noofkidslayout.setVisibility(View.GONE);
				} else {
					noofkidslayout.setVisibility(View.VISIBLE);
				}
			}

			@Override
			public void onNothingSelected(AdapterView<?> parent) {
				// TODO Auto-generated method stub

			}
		});

		GetUserProfileDetails();

		updateprofile.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (Validate()) {

					UpdateProfileDetails();
				}
			}
		});

		GetInterestDeatils();

		return rootView;
	}

	/**
	 * Update profile details.
	 */
	private void UpdateProfileDetails() {

		try {
			String url = StringURLs.EDITPROFILE;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			LocalData data = new LocalData(context);

			String genter = "m";

			if (male.isChecked() == true) {

				genter = "m";

			} else if (female.isChecked() == true) {

				genter = "f";
			}

			names.add("userid");
			values.add(data.GetS("userid"));
			names.add("firstname");
			values.add(firstname.getText().toString().trim());
			names.add("lastname");
			values.add(lastname.getText().toString().trim());
			names.add("email");
			values.add(email.getText().toString().trim());
			names.add("username");
			values.add(username.getText().toString());
			names.add("mobile");
			values.add(mobile.getText().toString().trim());

			if (newpassword.getText().toString().trim().length() != 0
					&& confirmpwd.getText().toString().trim().length() != 0) {
				names.add("password");
				values.add(newpassword.getText().toString().trim());
				names.add("password_confirmation");
				values.add(confirmpwd.getText().toString().trim());
			}

			String localname = "";

			if (firstname.getText().length() != 0) {
				localname = firstname.getText().toString();
			}

			if (lastname.getText().length() != 0) {
				localname = localname.length() == 0 ? lastname.getText()
						.toString() : localname + " "
						+ lastname.getText().toString();
			}

			if (username.getText().length() != 0) {

				if (localname.length() == 0)
					localname = username.getText().toString();
			}

			data.Update("name", localname);

			if (newpassword.getText().toString().trim().length() > 0) {
				data.Update("password", newpassword.getText().toString().trim());
			}

			names.add("gender");
			values.add(genter);
			names.add("dateofbirth");
			values.add(dob1.getText().toString().trim());
			names.add("facebookpage");
			values.add(facebook.getText().toString());
			names.add("twitterpage");
			values.add(twitter.getText().toString());
			names.add("instagrampage");
			values.add(instagram.getText().toString());

			names.add("hometown");
			values.add(hometown.getText().toString());
			names.add("school");
			values.add(school.getText().toString());
			names.add("status");
			values.add("A");
			names.add("occupation");
			values.add(occupation.getText().toString());
			names.add("maritalstatus");
			values.add(Integer.toString(maritalpos));
			names.add("noofkids");
			values.add(noofkids.getText().toString());
			names.add("favoriteholidayspot");
			values.add(favorite.getText().toString());
			names.add("interest_id");
			values.add("");
			names.add("timezone");
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

								Toast.makeText(context,
										"Successfully Updated.",
										Toast.LENGTH_LONG).show();

								Intent intent = new Intent(context,
										MyProfileActivity.class);
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

			if (file_name.length() > 0)
				connectServerImage.SendImage("profilepicture", file_name);

			connectServerImage.setMode(ConnectServerImage.MODE_POST);
			connectServerImage.setNames(names);
			connectServerImage.setValues(values);
			connectServerImage.execute(url);

		} catch (Exception exp) {

		}
	}

	private static int DOB = 15;

	/**
	 * Open date time picker dialogue.
	 */
	private void OpenDatePicker() {
		try {

			Intent intent = new Intent(context, DateTimePickerDialog.class);
			intent.putExtra("date", dob1.getText().toString() + " 01:01 am");
			intent.putExtra("enabletime", false);
			startActivityForResult(intent, DOB);

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
				uploadimage1.setText(filename);

				file_name = imgDecodableString;

				BitmapFactory.Options options = new BitmapFactory.Options();
				options.inPreferredConfig = Bitmap.Config.ARGB_8888;
				Bitmap bitmap = BitmapFactory.decodeFile(file_name, options);
				picture.setImageBitmap(bitmap);
			} else {
				uploadimage1.setText("");
				file_name = "";
				// contest_image2.setVisibility(View.VISIBLE);
				Toast.makeText(context, "You haven't picked Image",
						Toast.LENGTH_LONG).show();
			}

		} else if (requestCode == DOB && resultCode == Activity.RESULT_OK) {
			String date = data.getStringExtra("date");

			String[] dates = date.split(" ");

			dob1.setText(dates[0]);
		}
	}

	/**
	 * Validate fields.
	 * 
	 * @return
	 */
	private boolean Validate() {

		try {

			if (username.getText().toString().trim().length() == 0) {

				Toast.makeText(context, "Enter valid User Name.",
						Toast.LENGTH_LONG).show();
				username.requestFocus();
				return false;

			} else if (email.getText().toString().trim().length() == 0) {

				Toast.makeText(context, "Enter valid Email.", Toast.LENGTH_LONG)
						.show();
				email.requestFocus();
				return false;

			}

			if (newpassword.getText().toString().trim().length() != 0
					&& confirmpwd.getText().toString().trim().length() != 0) {

				if (newpassword.getText().length() < 5) {
					Toast.makeText(context,
							"The password must be at least 5 characters.",
							Toast.LENGTH_LONG).show();
					newpassword.requestFocus();
					return false;
				} else if (confirmpwd.getText().length() < 5) {
					Toast.makeText(context,
							"The password must be at least 5 characters.",
							Toast.LENGTH_LONG).show();
					confirmpwd.requestFocus();
					return false;
				} else if (newpassword.getText().toString().trim()
						.equals(confirmpwd.getText().toString().trim()) != true) {

					Toast.makeText(context, "Password mismatch.",
							Toast.LENGTH_LONG).show();
					newpassword.requestFocus();
					return false;
				}
			}

		} catch (Exception exp) {
			return false;
		}

		return true;
	}

	/**
	 * Get user profile details.
	 */
	private void GetUserProfileDetails() {

		try {

			String url = StringURLs.GETUSERPROFILE;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("userid");
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

						if (sJSON.length() > 0) {
							JSONObject jsonObject2 = new JSONObject(sJSON);

							if (jsonObject2.getJSONObject("response")
									.getString("success").equalsIgnoreCase("1") == true) {

								DisplayDatatoFileds(jsonObject2);

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
	 * Display existing details into fields.
	 * 
	 * @param jsonObject2
	 */
	private void DisplayDatatoFileds(JSONObject jsonObject2) {

		try {

			JSONObject jsonObject = jsonObject2.getJSONObject("profile");

			firstname.setText(jsonObject.getString("firstname"));
			lastname.setText(jsonObject.getString("lastname"));
			username.setText(jsonObject.getString("username"));

			// yyyy-mm-dd
			String date = jsonObject.getString("dateofbirth");
			SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
			SimpleDateFormat dateFormat1 = new SimpleDateFormat("dd-MMM-yyyy");

			Date date1 = dateFormat.parse(date);

			dob1.setText(dateFormat1.format(date1));

			email.setText(jsonObject.getString("email"));

			facebook.setText(jsonObject.getString("facebookpage"));
			favorite.setText(jsonObject.getString("favoriteholidayspot"));

			if (jsonObject.getString("gender").equalsIgnoreCase("m") == true) {
				male.setChecked(true);
			} else if (jsonObject.getString("gender").equalsIgnoreCase("f") == true) {
				female.setChecked(true);
			}

			hometown.setText(jsonObject.getString("hometown"));
			instagram.setText(jsonObject.getString("instagrampage"));

			marital.setSelection(Integer.parseInt(jsonObject
					.getString("maritalstatus")));

			if (marital.getSelectedItemPosition() == 0) {
				noofkidslayout.setVisibility(View.GONE);
			} else {
				noofkidslayout.setVisibility(View.VISIBLE);
			}

			/*
			 * if(jsonObject.getString("maritalstatus").equalsIgnoreCase("1") ==
			 * true) {
			 * 
			 * } else {
			 * 
			 * }
			 * 
			 * marital.setText(jsonObject.getString("maritalstatus"));
			 */

			mobile.setText(jsonObject.getString("mobile"));
			noofkids.setText(jsonObject.getString("noofkids"));
			occupation.setText(jsonObject.getString("occupation"));

			int loader = R.drawable.avator;
			ImageLoader imageLoader = new ImageLoader(context);
			imageLoader.DisplayImage(jsonObject.getString("profilepicture"),
					loader, picture);

			school.setText(jsonObject.getString("school"));
			timezone.setText(jsonObject.getString("timezone"));
			twitter.setText(jsonObject.getString("twitterpage"));

		} catch (Exception exp) {

			Toast.makeText(context, "Error", Toast.LENGTH_LONG).show();
		}
	}

	ArrayList<String> selectedinterest = new ArrayList<String>();

	/**
	 * Get interest details form server.
	 */
	private void GetInterestDeatils() {

		try {
			String url = StringURLs.GET_INTEREST;

			ConnectServer connectServer = new ConnectServer();
			connectServer.setContext(context);
			connectServer.setMode(ConnectServer.MODE_GET);
			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {

						if (sJSON.length() > 0) {

							Log.d("", sJSON);

							ShowInterestDeatails(sJSON);

							LinearLayout interrest = (LinearLayout) rootView
									.findViewById(R.id.interest);

							for (int j = 0; j < id.size(); j++) {

								CheckBox checkBox = new CheckBox(context);
								checkBox.setLayoutParams(new LayoutParams(280,
										45));
								checkBox.setOnClickListener(new OnClickListener() {

									@Override
									public void onClick(View v) {
										// TODO Auto-generated method stub

										boolean iChecked = ((CheckBox) v)
												.isChecked();
										CheckBox check = ((CheckBox) v);

										if (iChecked == true) {
											selectedinterest.add(check.getTag()
													.toString());
										} else {

											for (int i = 0; i < selectedinterest
													.size(); i++) {

												if (selectedinterest
														.get(i)
														.equalsIgnoreCase(
																check.getTag()
																		.toString()) == true) {
													selectedinterest
															.remove(check
																	.getTag()
																	.toString());
													return;
												}
											}
										}
									}
								});

								checkBox.setTextColor(Color.rgb(0, 0, 0));
								checkBox.setBackgroundColor(Color.rgb(255, 255,
										255));
								checkBox.setText(name.get(j));
								checkBox.setTag(id.get(j));

								interrest.addView(checkBox);
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

			connectServer.execute(url);
		} catch (Exception exp) {

		}
	}

	private ArrayList<Integer> id = new ArrayList<Integer>();
	private ArrayList<String> name = new ArrayList<String>();

	/**
	 * Show Interest details.
	 * 
	 * @param json
	 */
	private void ShowInterestDeatails(String json) {

		try {

			JSONObject jsonObject = new JSONObject(json);
			JSONObject response = jsonObject.getJSONObject("response");
			String success = response.getString("success");

			if (success.equalsIgnoreCase("1") == true) {
				JSONArray array = jsonObject.getJSONArray("interestdetails");

				for (int i = 0; i < array.length(); i++) {
					JSONObject interestdetails = array.getJSONObject(i);

					id.add(interestdetails.getInt("Interest_id"));
					name.add(interestdetails.getString("Interest_name"));
				}

				System.out.println("interest id name " + id + name);
			} else {
				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");

				Toast.makeText(context,
						Main.getStringResourceByName(context, msgcode),
						Toast.LENGTH_LONG).show();
			}

		} catch (Exception exp) {

			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}
}