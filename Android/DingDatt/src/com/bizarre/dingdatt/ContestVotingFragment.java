package com.bizarre.dingdatt;

import java.util.ArrayList;

import org.json.JSONObject;

import com.bizarre.dingdatt.database.DingDattDatabase;
import com.bizarre.dingdatt.imageloader.ImageLoader;
import com.bizarre.dingdatt.pojo.ContestGalleryPojo;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.media.MediaPlayer;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.text.InputType;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.View.OnTouchListener;
import android.view.ViewGroup;
import android.view.ViewGroup.LayoutParams;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.MediaController;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.VideoView;

/**
 * This used for voting a contest
 * 
 * @author karthik
 *
 */
public class ContestVotingFragment extends Fragment {

	private FragmentActivity context;
	private View rootView;
	LayoutInflater inflater;

	TextView title;
	LinearLayout image;
	Button x;
	Button ding;
	ImageView flag;
	TextView pass;
	ImageView createdbyimage;
	TextView createdbyname;
	// Button follow;
	ArrayList<String> contestparticipantids = new ArrayList<String>();

	String contest_id = "";
	String contest_name = "";
	String type = "";
	String contestparticipantid = "";
	ArrayList<ContestGalleryPojo> contestGalleryPojos = new ArrayList<ContestGalleryPojo>();

	MediaPlayer dingmp;
	MediaPlayer errmp;

	float mPreviousX;
	float dx;

	public ContestVotingFragment(FragmentActivity context, String contest_id,
			String id, String contest_name, String type) {

		this.context = context;
		this.contest_id = contest_id;
		this.contestparticipantid = id;
		this.contest_name = contest_name;
		this.type = type;
	}

	@Override
	public View onCreateView(final LayoutInflater inflater1,
			ViewGroup container, Bundle savedInstanceState) {
		this.inflater = inflater1;
		rootView = inflater.inflate(R.layout.contest_voting, container, false);

		Main.SetAdvertisment(rootView);

		title = (TextView) rootView.findViewById(R.id.title);
		image = (LinearLayout) rootView.findViewById(R.id.image);

		x = (Button) rootView.findViewById(R.id.x);
		ding = (Button) rootView.findViewById(R.id.ding);
		pass = (TextView) rootView.findViewById(R.id.pass);
		/* follow = (Button) rootView.findViewById(R.id.follow); */
		flag = (ImageView) rootView.findViewById(R.id.flag);

		ding.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				UpdateVoteintoDatabase(DING);
			}
		});

		x.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				UpdateVoteintoDatabase(ERR);
			}
		});

		flag.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				ReporttoAdmin();
			}
		});

		dingmp = MediaPlayer.create(context, R.raw.ding);
		errmp = MediaPlayer.create(context, R.raw.err);

		createdbyimage = (ImageView) rootView.findViewById(R.id.createdbyimage);
		createdbyname = (TextView) rootView.findViewById(R.id.createdbyname);

		initListener();
		GetGalleryDetailsFromDatabase();

		int iPos = contestparticipantids.indexOf(contestparticipantid);

		ShowNextDetailsintoScreen(iPos);

		return rootView;
	}

	/**
	 * Report the participated contest to admin
	 */
	private void ReporttoAdmin() {

		AlertDialog.Builder builder = new AlertDialog.Builder(context);
		builder.setTitle("Report");

		// Set up the input
		final EditText input = new EditText(context);

		input.setInputType(InputType.TYPE_CLASS_TEXT);
		input.setHint("Why do you want to report?");

		builder.setView(input);

		// Set up the buttons
		builder.setPositiveButton("Submit",
				new DialogInterface.OnClickListener() {
					@Override
					public void onClick(DialogInterface dialog, int which) {

						ReportSendtoServer(input.getText().toString());
					}
				});
		builder.setNegativeButton("Cancel",
				new DialogInterface.OnClickListener() {
					@Override
					public void onClick(DialogInterface dialog, int which) {

						dialog.cancel();
					}
				});

		builder.show();
	}

	/**
	 * Report send to server
	 * 
	 * @param text
	 *            - report text
	 */
	private void ReportSendtoServer(String text) {

		String url = StringURLs.REPORTFLAG;

		ArrayList<String> names = new ArrayList<String>();
		ArrayList<String> values = new ArrayList<String>();

		names.add("user_id");
		names.add("reporteddata");
		names.add("participantid");
		names.add("timezone");

		LocalData data = new LocalData(context);
		ContestGalleryPojo contestGalleryPojo = contestGalleryPojos
				.get(position);

		values.add(data.GetS("userid"));
		values.add(text);
		values.add(contestGalleryPojo.getId());
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
							String msgcode = jsonObject.getJSONObject(
									"response").getString("msgcode");

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											msgcode), Toast.LENGTH_LONG).show();

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
	 * 
	 */
	private void initListener() {

		pass.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				UpdateVoteintoDatabase(PASS);
			}
		});
	}

	private static String DING = "L";
	private static String ERR = "U";
	private static String PASS = "P";

	/**
	 * Selected vote update into database(TABLE-gallery)
	 * 
	 * @param vote
	 *            DING, ERR or PASS
	 */
	private void UpdateVoteintoDatabase(String vote) {

		SQLiteDatabase database = SQLiteDatabase.openDatabase(
				DingDattDatabase.DATABASE_NAME, null,
				SQLiteDatabase.OPEN_READWRITE);

		try {

			if (vote.equalsIgnoreCase(DING)) {
				dingmp.start();
			} else if (vote.equalsIgnoreCase(ERR)) {
				errmp.start();
			}

			String query = "UPDATE " + DingDattDatabase.TABLE_GALLERY + " SET "
					+ DingDattDatabase.FIELD_GALLERY_VOTINGSTATUS + " = '"
					+ vote + "'" + ", "
					+ DingDattDatabase.FIELD_GALLERY_UPLOADEDSTATUS + "= '" + 0
					+ "'" + " WHERE " + DingDattDatabase.FIELD_GALLERY_ID
					+ " = '" + contestparticipantid + "'";

			System.out.print("QUERY " + query);

			database.execSQL(query);
			database.close();

			GetGalleryDetailsFromDatabase();

			int iPos = contestparticipantids.indexOf(contestparticipantid);

			ShowNextDetailsintoScreen(iPos);

		} catch (Exception exp) {
			System.out.print(exp.getMessage());
		}
	}

	int position = -1;

	/**
	 * Show details into screen after every vote completed.
	 * 
	 * @param iPos
	 */
	private void ShowNextDetailsintoScreen(int iPos) {

		try {

			image.removeAllViews();

			if (contestGalleryPojos.size() > 0) {
				if (iPos == contestparticipantids.size() || iPos == -1) {
					iPos = 0;
				}

				position = iPos;

				ContestGalleryPojo contestGalleryPojo = contestGalleryPojos
						.get(iPos);

				contestparticipantid = contestGalleryPojo.getId();

				if (contestGalleryPojo.getVotingstatus().equalsIgnoreCase("") == true) {
					title.setText(contest_name);

					if (type.equalsIgnoreCase("p") == true) {
						int loader = R.drawable.loader;
						ImageLoader imgLoader = new ImageLoader(context);
						ImageView imageView = new ImageView(context);
						imageView.setLayoutParams(new LayoutParams(
								LayoutParams.MATCH_PARENT,
								LayoutParams.MATCH_PARENT));

						imgLoader.DisplayImage(
								contestGalleryPojo.getUploadfile(), loader,
								imageView);

						image.addView(imageView);

					} else if (type.equalsIgnoreCase("v") == true) {

						MediaController mediaControls = new MediaController(
								context);

						VideoView video = new VideoView(context);
						video.setLayoutParams(new LayoutParams(
								LayoutParams.MATCH_PARENT,
								LayoutParams.MATCH_PARENT));
						// mediaControls.setLayoutParams(new
						// LayoutParams(video.getWidth(), video.getHeight()));
						video.setMediaController(mediaControls);
						video.setVideoURI(Uri.parse(contestGalleryPojo
								.getUploadfile()));
						video.start();

						image.addView(video);

					} else if (type.equalsIgnoreCase("t") == true) {
						TextView textView = new TextView(context);
						textView.setLayoutParams(new LayoutParams(
								LayoutParams.MATCH_PARENT,
								LayoutParams.MATCH_PARENT));
						textView.setPadding(100, 0, 0, 0);
						textView.setText(contestGalleryPojo.getUploadtopic());
						image.addView(textView);
					}

					int loader1 = R.drawable.avator;
					ImageLoader imgLoader1 = new ImageLoader(context);
					imgLoader1.DisplayImage(contestGalleryPojo.getProfilepic(),
							loader1, createdbyimage);

					createdbyname.setText(contestGalleryPojo.getName());

					if (contestGalleryPojo.getFollowing() == 1) {
						// Toast.makeText(context, "following = 1",
						// Toast.LENGTH_LONG).show();
						int imgResource = R.drawable.bell_green;
						createdbyname.setCompoundDrawablesWithIntrinsicBounds(
								0, 0, imgResource, 0);
						/*
						 * follow.setTag(""); follow.setVisibility(View.GONE);
						 */
					} else {
						// Toast.makeText(context, "following = 0",
						// Toast.LENGTH_LONG).show();
						createdbyname.setCompoundDrawablesWithIntrinsicBounds(
								0, 0, 0, 0);
						/*
						 * follow.setTag(contestGalleryPojo.getUser_id());
						 * follow.setVisibility(View.VISIBLE);
						 */
					}
				} else {
					// Toast.makeText(context, "Voting completed.",
					// Toast.LENGTH_LONG).show();

					Intent intent = new Intent(context,
							ContestInfoSampleActivity.class);
					intent.putExtra("contest_id", contest_id);
					startActivity(intent);
					context.finish();
				}
			}

		} catch (Exception exp) {

		}

	}

	/**
	 * Get gallery details from database.
	 */
	private void GetGalleryDetailsFromDatabase() {

		SQLiteDatabase database = SQLiteDatabase.openDatabase(
				DingDattDatabase.DATABASE_NAME, null,
				SQLiteDatabase.OPEN_READWRITE);

		Cursor cursor = null;

		contestGalleryPojos = new ArrayList<ContestGalleryPojo>();
		contestparticipantids = new ArrayList<String>();

		try {
			String sql = "SELECT " + DingDattDatabase.FIELD_GALLERY_ID + ", "
					+ DingDattDatabase.FIELD_GALLERY_UPLOADDATE + ", "
					+ DingDattDatabase.FIELD_GALLERY_UPLOADTOPIC + ", "
					+ DingDattDatabase.FIELD_GALLERY_USER_ID + ", "
					+ DingDattDatabase.FIELD_GALLERY_VOTINGSTATUS + ", "
					+ DingDattDatabase.FIELD_GALLERY_FOLLOWING + ", "
					+ DingDattDatabase.FIELD_GALLERY_NAME + ", "
					+ DingDattDatabase.FIELD_GALLERY_PROFILEPICTURE + ", "
					+ DingDattDatabase.FIELD_GALLERY_UPLOADFILE + " FROM "
					+ DingDattDatabase.TABLE_GALLERY + "" + " WHERE "
					+ DingDattDatabase.FIELD_GALLERY_CONTEST_ID + " = '"
					+ contest_id + "'" + " AND "
					+ DingDattDatabase.FIELD_GALLERY_UPLOADEDSTATUS + " = '"
					+ 0 + "'" + " AND "
					+ DingDattDatabase.FIELD_GALLERY_VOTINGSTATUS
					+ " NOT IN ('L', 'U', 'P')";

			cursor = database.rawQuery(sql, null);

			if (cursor != null && cursor.getCount() > 0) {

				cursor.moveToFirst();

				do {

					ContestGalleryPojo contestGalleryPojo = new ContestGalleryPojo();

					contestparticipantids
							.add(cursor.getString(cursor
									.getColumnIndex(DingDattDatabase.FIELD_GALLERY_ID)));

					contestGalleryPojo
							.setId(cursor.getString(cursor
									.getColumnIndex(DingDattDatabase.FIELD_GALLERY_ID)));
					contestGalleryPojo
							.setUploaddate(cursor.getString(cursor
									.getColumnIndex(DingDattDatabase.FIELD_GALLERY_UPLOADDATE)));
					contestGalleryPojo
							.setUploadfile(cursor.getString(cursor
									.getColumnIndex(DingDattDatabase.FIELD_GALLERY_UPLOADFILE)));
					contestGalleryPojo
							.setUploadtopic(cursor.getString(cursor
									.getColumnIndex(DingDattDatabase.FIELD_GALLERY_UPLOADTOPIC)));
					contestGalleryPojo
							.setUser_id(cursor.getString(cursor
									.getColumnIndex(DingDattDatabase.FIELD_GALLERY_USER_ID)));

					contestGalleryPojo
							.setVotingstatus(cursor.getString(cursor
									.getColumnIndex(DingDattDatabase.FIELD_GALLERY_VOTINGSTATUS)));
					contestGalleryPojo
							.setFollowing(cursor.getInt(cursor
									.getColumnIndex(DingDattDatabase.FIELD_GALLERY_FOLLOWING)));

					contestGalleryPojo
							.setName(cursor.getString(cursor
									.getColumnIndex(DingDattDatabase.FIELD_GALLERY_NAME)));
					contestGalleryPojo
							.setProfilepic(cursor.getString(cursor
									.getColumnIndex(DingDattDatabase.FIELD_GALLERY_PROFILEPICTURE)));

					contestGalleryPojos.add(contestGalleryPojo);

				} while (cursor.moveToNext());
			} else {
				// Toast.makeText(context, "Voting completed.",
				// Toast.LENGTH_LONG).show();

				Intent intent = new Intent(context,
						ContestInfoSampleActivity.class);
				intent.putExtra("contest_id", Integer.parseInt(contest_id));
				startActivity(intent);
				context.finish();
			}

		} catch (Exception exp) {

		} finally {
			if (cursor != null) {
				cursor.close();
			}

			if (database != null) {
				database.close();
			}
		}
	}
}