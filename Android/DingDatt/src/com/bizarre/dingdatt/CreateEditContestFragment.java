package com.bizarre.dingdatt;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

import org.json.JSONArray;
import org.json.JSONObject;

import com.bizarre.dingdatt.imageloader.ImageLoader;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;

import android.app.Activity;
import android.content.Intent;
import android.database.Cursor;
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
import android.view.ViewGroup;
import android.view.ViewGroup.LayoutParams;
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
public class CreateEditContestFragment extends Fragment {

	private FragmentActivity context;
	private View rootView;
	LayoutInflater inflater;
	Button create_contest;
	TextView contest_image;
	ImageView contest_image1;
	ImageView contest_image2;
	Spinner contest_type;
	ImageView contest_type1;
	EditText contest_prize;
	EditText contest_info;
	EditText contest_name;
	EditText noofparticipant;
	String file_name = "";

	TextView contest_start_date1;
	ImageView contest_start_date2;
	TextView contest_end_date1;
	ImageView contest_end_date2;

	TextView voting_start_date1;
	ImageView voting_start_date2;
	TextView voting_end_date1;
	ImageView voting_end_date2;

	private int CONTEST_IMAGE = 1;
	int type = 0;
	int contest_id = -1;

	private ArrayList<String> contest_type_array = new ArrayList<String>();
	private ArrayList<String> contest_type_array1 = new ArrayList<String>();

	public CreateEditContestFragment(FragmentActivity context, int type,
			int contest_id) {

		this.context = context;
		this.type = type;
		this.contest_id = contest_id;
	}

	@Override
	public View onCreateView(final LayoutInflater inflater1,
			ViewGroup container, Bundle savedInstanceState) {

		this.inflater = inflater1;
		rootView = inflater.inflate(R.layout.create_edit_contest, container,
				false);

		Main.SetAdvertisment(rootView);

		create_contest = (Button) rootView.findViewById(R.id.create_contest);
		contest_image = (TextView) rootView.findViewById(R.id.contest_image);
		contest_image1 = (ImageView) rootView.findViewById(R.id.contest_image1);
		contest_image2 = (ImageView) rootView.findViewById(R.id.contest_image2);
		contest_type = (Spinner) rootView.findViewById(R.id.contest_type);
		contest_type1 = (ImageView) rootView.findViewById(R.id.contest_type1);

		create_contest.setTextColor(Color.parseColor("#ffffff"));

		contest_prize = (EditText) rootView.findViewById(R.id.contest_prize);
		contest_info = (EditText) rootView.findViewById(R.id.contest_info);
		contest_name = (EditText) rootView.findViewById(R.id.contest_name);
		noofparticipant = (EditText) rootView
				.findViewById(R.id.noofparticipant);

		InitDateFields();

		contest_type_array.add("Photo");
		contest_type_array.add("Video");
		contest_type_array.add("Topic");

		contest_type_array1.add("p");
		contest_type_array1.add("v");
		contest_type_array1.add("t");

		ArrayAdapter<String> adapter_array = new ArrayAdapter<String>(context,
				R.layout.spinner_item_view, contest_type_array);
		contest_type.setAdapter(adapter_array);

		contest_type1.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				contest_type.performClick();
			}
		});

		contest_image.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				Intent galleryIntent = new Intent(
						Intent.ACTION_PICK,
						android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
				// Start the Intent
				startActivityForResult(galleryIntent, CONTEST_IMAGE);

				/*
				 * Intent intent = new
				 * Intent(android.provider.MediaStore.ACTION_IMAGE_CAPTURE);
				 * startActivityForResult(intent,CONTEST_IMAGE);
				 */
			}
		});

		contest_image1.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				Intent galleryIntent = new Intent(
						Intent.ACTION_PICK,
						android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
				// Start the Intent
				startActivityForResult(galleryIntent, CONTEST_IMAGE);

				/*
				 * Intent intent = new
				 * Intent(android.provider.MediaStore.ACTION_IMAGE_CAPTURE);
				 * startActivityForResult(intent,CONTEST_IMAGE);
				 */
			}
		});

		create_contest.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (validate()) {

					if (type == 0) {
						CreateContest();
					} else if (type == 1) {
						UpdateContest();
					}
				}
			}
		});

		GetInterestDetails();

		if (type == 1) {

			GetContestDeatailsFromServer();
		}

		return rootView;
	}

	/**
	 * Get Contest details from server.
	 */
	private void GetContestDeatailsFromServer() {

		try {
			String url = StringURLs.GET_CONTEST_FOR_EDIT;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("contestid");
			names.add("timezone");

			values.add(Integer.toString(contest_id));
			values.add(Main.GetTimeZone());

			url = StringURLs.getQuery(url, names, values);

			ConnectServer connectServer = new ConnectServer();
			connectServer.setContext(context);
			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {

						if (sJSON.length() > 0) {

							GetContestDetailsFromJSON(sJSON);

						} else {
							ShowMessage("c100", "");
						}

					} catch (Exception exp) {

						ShowMessage("c100", "");
					}
				}
			});

			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.execute(url);
		} catch (Exception exp) {

		}
	}

	/**
	 * 
	 * @param code
	 * @param message
	 */
	private void ShowMessage(String code, String message) {

		String msg = Main.getStringResourceByName(context, code);

		if (msg.equalsIgnoreCase("Error") == true)
			Toast.makeText(context, message, Toast.LENGTH_LONG).show();
		else
			Toast.makeText(context, msg, Toast.LENGTH_LONG).show();
	}

	ArrayList<String> selectedinterest = new ArrayList<String>();

	/**
	 * Get interest details
	 */
	private void GetInterestDetails() {

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

							GetInterestDetailsFromJSON(sJSON);

							LinearLayout interrest = (LinearLayout) rootView
									.findViewById(R.id.interrest);

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
	 * Get Interest details from JSON string
	 * 
	 * @param json
	 *            - JSON string
	 */
	private void GetInterestDetailsFromJSON(String json) {

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

	/**
	 * Get contest details from json string
	 * 
	 * @param sJSON
	 *            - json string
	 */
	private void GetContestDetailsFromJSON(String sJSON) {

		try {

			JSONObject jsonObject = new JSONObject(sJSON);

			JSONObject response = jsonObject.getJSONObject("response");

			String success = response.getString("success");

			if (success.equalsIgnoreCase("1")) {

				JSONObject contestparticipantcount = jsonObject
						.getJSONObject("contestparticipantcount");
				int contestparticipantcount1 = contestparticipantcount
						.getInt("contestparticipantcount");

				SimpleDateFormat formatter = new SimpleDateFormat(
						"dd-MM-yyyy hh:mm a"); // yyyy-MM-dd HH:mm
				SimpleDateFormat formatter1 = new SimpleDateFormat(
						"yyyy-MM-dd HH:mm:ss"); // yyyy-MM-dd HH:mm

				JSONObject contestdetails = jsonObject
						.getJSONObject("contest Details");

				contest_id = contestdetails.getInt("ID");

				contest_name.setText(contestdetails.getString("contest_name"));
				contest_info.setText(contestdetails.getString("description"));

				Date date1 = formatter1.parse(contestdetails
						.getString("conteststartdate"));

				contest_start_date1.setText(formatter.format(date1));

				Date date2 = formatter1.parse(contestdetails
						.getString("contestenddate"));

				contest_end_date1.setText(formatter.format(date2));

				Date date3 = formatter1.parse(contestdetails
						.getString("votingstartdate"));

				voting_start_date1.setText(formatter.format(date3));

				Date date4 = formatter1.parse(contestdetails
						.getString("votingenddate"));

				voting_end_date1.setText(formatter.format(date4));

				contest_prize.setText(contestdetails.getString("prize"));

				int loader = R.drawable.loader;
				String image_url = contestdetails.getString("themephoto");
				ImageLoader imgLoader = new ImageLoader(context);

				imgLoader.DisplayImage(image_url, loader, contest_image2);
				contest_image.setText(contestdetails.getString("themephoto"));
				contest_image2.setVisibility(View.VISIBLE);

				noofparticipant.setText(Integer.toString(contestdetails
						.getInt("noofparticipant")));

				String type = contestdetails.getString("contesttype");
				int pos = contest_type_array1.indexOf(type);

				contest_type.setSelection(pos);

				create_contest.setText("Update Contest");

				JSONArray interest = contestdetails.getJSONArray("interest");

				for (int j = 0; j < interest.length(); j++) {

					JSONObject interestobj = interest.getJSONObject(j);
					LinearLayout interrest = (LinearLayout) rootView
							.findViewById(R.id.interrest);

					for (int k = 0; k < id.size(); k++) {
						CheckBox checkBox = ((CheckBox) interrest.getChildAt(k));

						if (checkBox
								.getText()
								.toString()
								.equalsIgnoreCase(
										interestobj.getString("Interest_name")) == true) {
							checkBox.setChecked(true);
							checkBox.setTag(id.get(k).toString());
							selectedinterest.add(id.get(k).toString());
						}
					}
				}

				if (contestparticipantcount1 > 0) {

					Main.Enable(false, contest_name);
					Main.Enable(false, contest_image);
					// Main.Enable(false, contest_image1);
					// Main.Enable(false, contest_image2);
					contest_image1.setClickable(false);
					contest_image1.setFocusable(false);
					Main.Enable(false, contest_prize);
					Main.Enable(false, contest_type);
					Main.Enable(false, noofparticipant);
				}

				SimpleDateFormat formatter3 = new SimpleDateFormat(
						"dd-MM-yyyy hh:mm a"); // yyyy-MM-dd HH:mm

				Date date = new Date();
				Date conteststartdate = formatter3.parse(contest_start_date1
						.getText().toString());
				Date contestenddate = formatter3.parse(contest_end_date1
						.getText().toString());

				Date votingstartdate = formatter3.parse(voting_start_date1
						.getText().toString());
				Date votingenddate = formatter3.parse(voting_end_date1
						.getText().toString());

				if (date.compareTo(conteststartdate) >= 0) {
					Main.Enable(false, contest_start_date1);
				}

				if (date.compareTo(contestenddate) >= 0) {
					Main.Enable(false, contest_end_date1);
				}

				if (date.compareTo(votingstartdate) >= 0) {
					Main.Enable(false, voting_start_date1);
				}

				if (date.compareTo(votingenddate) >= 0) {
					Main.Enable(false, voting_end_date1);
				}

			} else {

				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");
				ShowMessage(msgcode, jsonObject.getJSONObject("response")
						.getString("message"));
			}

		} catch (Exception exp) {

			ShowMessage("c100", "");
		}
	}

	/**
	 * Validate fields.
	 * 
	 * @return
	 */
	private boolean validate() {

		Date csd = null, ced = null, vsd = null, ved = null;

		SimpleDateFormat formatter = new SimpleDateFormat("dd-MM-yyyy hh:mm a"); // yyyy-MM-dd
																					// HH:mm

		try {

			if (contest_start_date1.getText().length() > 0) {

				csd = formatter.parse(contest_start_date1.getText().toString());
			}

			if (contest_end_date1.getText().length() > 0) {
				ced = formatter.parse(contest_end_date1.getText().toString());
			}

			if (voting_start_date1.getText().length() > 0) {
				vsd = formatter.parse(voting_start_date1.getText().toString());
			}

			if (voting_end_date1.getText().length() > 0) {
				ved = formatter.parse(voting_end_date1.getText().toString());
			}

			if (contest_name.getText().toString().length() == 0) {

				Toast.makeText(context, "Enter valid Contest Name",
						Toast.LENGTH_SHORT).show();
				contest_name.requestFocus();
				return false;

			} else if (contest_image.getText().toString().length() == 0) {

				Toast.makeText(context, "Upload Contest Image",
						Toast.LENGTH_SHORT).show();
				return false;
			} else if (contest_type.getSelectedItemPosition() == -1) {

				Toast.makeText(context, "Enter valid Contest Type",
						Toast.LENGTH_SHORT).show();
				return false;
			} else if (contest_start_date1.getText().toString().length() == 0) {

				Toast.makeText(context, "Enter valid Contest Start Date",
						Toast.LENGTH_SHORT).show();
				return false;
			} else if (contest_end_date1.getText().toString().length() == 0) {

				Toast.makeText(context, "Enter valid Contest End Date",
						Toast.LENGTH_SHORT).show();
				return false;
			} else if (voting_start_date1.getText().toString().length() == 0) {

				Toast.makeText(context, "Enter valid Voting Start Date",
						Toast.LENGTH_SHORT).show();
				return false;
			} else if (voting_end_date1.getText().toString().length() == 0) {

				Toast.makeText(context, "Enter valid Voting End Date",
						Toast.LENGTH_SHORT).show();
				return false;
			} else if (csd.after(ced)) {

				Toast.makeText(context,
						"Contest End date greater then Start date.",
						Toast.LENGTH_SHORT).show();
				return false;
			} else if (ced.compareTo(vsd) != 0 || ced.after(vsd)) {

				Toast.makeText(context,
						"Voting Start date greater then Contest End date.",
						Toast.LENGTH_SHORT).show();
				return false;
			} else if (vsd.after(ved)) {

				Toast.makeText(context,
						"Voting End date greater then Start date.",
						Toast.LENGTH_SHORT).show();
				return false;
			}
			/*
			 * else if(contest_prize.getText().toString().length() == 0) {
			 * 
			 * Toast.makeText(context, "Enter valid Contest Prize",
			 * Toast.LENGTH_SHORT).show(); contest_prize.requestFocus(); return
			 * false; } else if(contest_info.getText().toString().length() == 0)
			 * {
			 * 
			 * Toast.makeText(context, "Enter valid Contest Information",
			 * Toast.LENGTH_SHORT).show(); contest_info.requestFocus(); return
			 * false; }
			 */else if (noofparticipant.getText().toString().length() == 0) {

				Toast.makeText(context, "Enter valid no. of participants.",
						Toast.LENGTH_SHORT).show();
				noofparticipant.requestFocus();
				return false;
			}/*
			 * else if(file_name.length() == 0) { Toast.makeText(context,
			 * "Please upload theme photo.", Toast.LENGTH_SHORT).show(); return
			 * false; }
			 */

			return true;
		} catch (Exception exp) {
			return false;
		}
	}

	/**
	 * 
	 */
	private void InitDateFields() {

		try {

			contest_start_date1 = (TextView) rootView
					.findViewById(R.id.contest_start_date1);
			contest_start_date2 = (ImageView) rootView
					.findViewById(R.id.contest_start_date2);
			contest_end_date1 = (TextView) rootView
					.findViewById(R.id.contest_end_date1);
			contest_end_date2 = (ImageView) rootView
					.findViewById(R.id.contest_end_date2);
			voting_start_date1 = (TextView) rootView
					.findViewById(R.id.voting_start_date1);
			voting_start_date2 = (ImageView) rootView
					.findViewById(R.id.voting_start_date2);
			voting_end_date1 = (TextView) rootView
					.findViewById(R.id.voting_end_date1);
			voting_end_date2 = (ImageView) rootView
					.findViewById(R.id.voting_end_date2);

			contest_start_date1.setTextColor(Color.parseColor("#000000"));
			contest_end_date1.setTextColor(Color.parseColor("#000000"));
			voting_start_date1.setTextColor(Color.parseColor("#000000"));
			voting_end_date1.setTextColor(Color.parseColor("#000000"));

			contest_start_date1.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					// TODO Auto-generated method stub

					Intent intent = new Intent(context,
							DateTimePickerDialog.class);
					intent.putExtra("date", contest_start_date1.getText()
							.toString());
					intent.putExtra("predate", "");
					startActivityForResult(intent, 11);
				}
			});

			contest_start_date2.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					// TODO Auto-generated method stub

					Intent intent = new Intent(context,
							DateTimePickerDialog.class);
					intent.putExtra("date", contest_start_date1.getText()
							.toString());
					intent.putExtra("predate", "");
					startActivityForResult(intent, 11);
				}
			});

			contest_end_date1.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					// TODO Auto-generated method stub

					Intent intent = new Intent(context,
							DateTimePickerDialog.class);
					intent.putExtra("date", contest_end_date1.getText()
							.toString());
					intent.putExtra("predate", contest_start_date1.getText()
							.toString());
					startActivityForResult(intent, 12);
				}
			});
			contest_end_date2.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					// TODO Auto-generated method stub

					Intent intent = new Intent(context,
							DateTimePickerDialog.class);
					intent.putExtra("date", contest_end_date1.getText()
							.toString());
					intent.putExtra("predate", contest_start_date1.getText()
							.toString());
					startActivityForResult(intent, 12);
				}
			});
			voting_start_date1.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					// TODO Auto-generated method stub

					Intent intent = new Intent(context,
							DateTimePickerDialog.class);
					intent.putExtra("date", voting_start_date1.getText()
							.toString());
					intent.putExtra("predate", contest_end_date1.getText()
							.toString());
					startActivityForResult(intent, 13);
				}
			});
			voting_start_date2.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					// TODO Auto-generated method stub

					Intent intent = new Intent(context,
							DateTimePickerDialog.class);
					intent.putExtra("date", voting_start_date1.getText()
							.toString());
					intent.putExtra("predate", contest_end_date1.getText()
							.toString());
					startActivityForResult(intent, 13);
				}
			});
			voting_end_date1.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					// TODO Auto-generated method stub

					Intent intent = new Intent(context,
							DateTimePickerDialog.class);
					intent.putExtra("date", voting_end_date1.getText()
							.toString());
					intent.putExtra("predate", voting_start_date1.getText()
							.toString());
					startActivityForResult(intent, 14);
				}
			});
			voting_end_date2.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					// TODO Auto-generated method stub

					Intent intent = new Intent(context,
							DateTimePickerDialog.class);
					intent.putExtra("date", voting_end_date1.getText()
							.toString());
					intent.putExtra("predate", voting_start_date1.getText()
							.toString());
					startActivityForResult(intent, 14);
				}
			});

		} catch (Exception exp) {

		}
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		// TODO Auto-generated method stub
		super.onActivityResult(requestCode, resultCode, data);

		if (requestCode == CONTEST_IMAGE && null != data) {
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
				contest_image.setText(filename);

				file_name = imgDecodableString;
			} else {
				contest_image.setText("");
				file_name = "";
				contest_image2.setVisibility(View.VISIBLE);
				Toast.makeText(context, "You haven't picked Image",
						Toast.LENGTH_LONG).show();
			}

		} else if (requestCode == 11) {

			if (resultCode == Activity.RESULT_OK) {

				contest_start_date1.setText(data.getStringExtra("date"));
			}
		} else if (requestCode == 12) {

			if (resultCode == Activity.RESULT_OK) {
				try {

					String dt = data.getStringExtra("date"); // Start date
					SimpleDateFormat sdf = new SimpleDateFormat(
							"dd-MM-yyyy hh:mm a");
					Calendar c = Calendar.getInstance();
					Date date = new Date(sdf.parse(dt).getTime());
					c.setTime(sdf.parse(dt));
					c.add(Calendar.DATE, 1); // number of days to add
					dt = sdf.format(c.getTime()); // dt is now the new date

					Date date2 = sdf.parse(data.getStringExtra("date"));
					contest_end_date1.setText(sdf.format(date2));
					voting_start_date1.setText(sdf.format(date));
					voting_end_date1.setText(dt);

				} catch (Exception exp) {

				}

			}
		} else if (requestCode == 13) {

			if (resultCode == Activity.RESULT_OK) {

				voting_start_date1.setText(data.getStringExtra("date"));
			}
		} else if (requestCode == 14) {

			if (resultCode == Activity.RESULT_OK) {

				voting_end_date1.setText(data.getStringExtra("date"));
			}
		}
	}

	/**
	 * Create contest
	 */
	private void CreateContest() {

		try {

			ArrayList<String> asName = new ArrayList<String>();

			asName.add("contest_name");
			asName.add("description");
			asName.add("noofparticipant");
			asName.add("conteststartdate");
			asName.add("contestenddate");
			asName.add("votingstartdate");
			asName.add("votingenddate");
			asName.add("contesttype");
			asName.add("status");
			asName.add("userid");
			asName.add("prize");
			asName.add("interest_id");
			asName.add("timezone");

			ArrayList<String> asValue = new ArrayList<String>();

			String sContestType = contest_type_array1.get(contest_type_array
					.indexOf(contest_type.getSelectedItem().toString()));

			asValue.add(contest_name.getText().toString().trim());
			asValue.add(contest_info.getText().toString().trim());
			asValue.add(noofparticipant.getText().toString().trim());
			asValue.add(contest_start_date1.getText().toString());
			asValue.add(contest_end_date1.getText().toString());
			asValue.add(voting_start_date1.getText().toString());
			asValue.add(voting_end_date1.getText().toString());
			asValue.add(sContestType);
			asValue.add("1");
			asValue.add((new LocalData(context)).GetS("userid"));
			asValue.add(contest_prize.getText().toString().trim());

			String interest = "";

			for (int i = 0; i < selectedinterest.size(); i++) {

				if (interest.length() == 0) {

					interest = selectedinterest.get(i);
				} else {

					interest = interest + "," + selectedinterest.get(i);
				}
			}

			asValue.add(interest);
			asValue.add(Main.GetTimeZone());

			String sUrl = StringURLs.CREATE_CONTEST;
			/*
			 * sUrl = StringURLs.getQuery(sUrl, asName, asValue);
			 */
			ConnectServerImage connectServer = new ConnectServerImage();
			connectServer.setContext(context);
			connectServer.setNames(asName);
			connectServer.setValues(asValue);
			connectServer.SendImage("themephoto", file_name);
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

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();

								Intent intent = new Intent(context,
										ContestInfoSampleActivity.class);
								intent.putExtra("contest_id",
										jsonObject2.getInt("contest_id"));
								startActivity(intent);
								context.finish();

							} else {

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");
								ShowMessage(msgcode,
										jsonObject.getJSONObject("response")
												.getString("message"));
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

			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * Update Contest
	 */
	private void UpdateContest() {

		try {

			ArrayList<String> asName = new ArrayList<String>();

			asName.add("contest_name");
			asName.add("description");
			asName.add("noofparticipant");
			asName.add("conteststartdate");
			asName.add("contestenddate");
			asName.add("votingstartdate");
			asName.add("votingenddate");
			asName.add("contesttype");
			asName.add("status");
			asName.add("userid");
			asName.add("prize");
			asName.add("interest_id");
			asName.add("timezone");
			asName.add("contestid");

			ArrayList<String> asValue = new ArrayList<String>();

			String sContestType = contest_type_array1.get(contest_type_array
					.indexOf(contest_type.getSelectedItem().toString()));

			asValue.add(contest_name.getText().toString().trim());
			asValue.add(contest_info.getText().toString().trim());
			asValue.add(noofparticipant.getText().toString().trim());
			asValue.add(contest_start_date1.getText().toString());
			asValue.add(contest_end_date1.getText().toString());
			asValue.add(voting_start_date1.getText().toString());
			asValue.add(voting_end_date1.getText().toString());
			asValue.add(sContestType);
			asValue.add("1");
			asValue.add((new LocalData(context)).GetS("userid"));
			asValue.add(contest_prize.getText().toString().trim());

			String interest = "";

			for (int i = 0; i < selectedinterest.size(); i++) {

				if (interest.length() == 0) {

					interest = selectedinterest.get(i);
				} else {

					interest = interest + "," + selectedinterest.get(i);
				}
			}

			asValue.add(interest);
			asValue.add(Main.GetTimeZone());
			asValue.add(Integer.toString(contest_id));

			String sUrl = StringURLs.UPDATE_CONTEST;
			/*
			 * sUrl = StringURLs.getQuery(sUrl, asName, asValue);
			 */
			ConnectServerImage connectServer = new ConnectServerImage();
			connectServer.setContext(context);
			connectServer.setNames(asName);
			connectServer.setValues(asValue);
			connectServer.SendImage("themephoto", file_name);
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

								String msgcode = jsonObject.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										context,
										Main.getStringResourceByName(context,
												msgcode), Toast.LENGTH_LONG)
										.show();

								Intent intent = new Intent(context,
										MyContestActivity.class);
								context.startActivity(intent);
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
}