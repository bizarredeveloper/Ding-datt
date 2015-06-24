package com.bizarre.dingdatt;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONObject;

import com.bizarre.dingdatt.adapter.GalleryAdapter;
import com.bizarre.dingdatt.database.DingDattDatabase;
import com.bizarre.dingdatt.pojo.ContestGalleryPojo;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;

import android.content.Intent;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.GridView;

/**
 * This is used to show participated galleries like photo, video and topic.
 * 
 * @author karthik
 *
 */
public class ContestGalleryFragment extends Fragment {

	private FragmentActivity context;
	private View rootView;
	LayoutInflater inflater;
	String contest_id = "";
	String contest_name = "";
	String type = "";
	boolean bVote = false;
	GridView gridview;

	ArrayList<String> imagesurl = new ArrayList<String>();
	ArrayList<String> topics = new ArrayList<String>();
	ArrayList<String> ids = new ArrayList<String>();
	ArrayList<ContestGalleryPojo> contestGalleryPojos = new ArrayList<ContestGalleryPojo>();

	public ContestGalleryFragment(FragmentActivity context, String contest_id,
			String contest_name, String type, boolean vote) {

		this.context = context;
		this.contest_id = contest_id;
		this.contest_name = contest_name;
		this.type = type;
		this.bVote = vote;
	}

	@Override
	public View onCreateView(final LayoutInflater inflater1,
			ViewGroup container, Bundle savedInstanceState) {
		this.inflater = inflater1;
		rootView = inflater.inflate(R.layout.contest_gallery, container, false);

		Main.SetAdvertisment(rootView);

		gridview = (GridView) rootView.findViewById(R.id.gridview);

		((TextView) rootView.findViewById(R.id.title)).setText(contest_name);

		if (bVote == true) {

			GetContesDetailsFromDatabase();

			GalleryAdapter adapter = new GalleryAdapter(context, imagesurl,
					topics, ids, type);
			gridview.setAdapter(adapter);

		} else {

			GetContestGalleryDetails();
		}

		gridview.setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
					long arg3) {

				if (bVote == true) {
					Intent intent = new Intent(context,
							ContestVotingActivity.class);
					intent.putExtra("contest_id", contest_id);
					intent.putExtra("name", contest_name);
					intent.putExtra("type", type);
					intent.putExtra("id", ids.get(arg2));
					startActivity(intent);
					context.finish();
				} else if (bVote == false) {
					Intent intent = new Intent(context, CommentActivity.class);
					intent.putExtra("contest_id", contest_id);
					intent.putExtra("name", contest_name);
					intent.putExtra("type", type);
					intent.putExtra("participantid",
							contestGalleryPojos.get(arg2).getUser_id());
					intent.putExtra("value", arg0.getAdapter().getItem(arg2)
							.toString());
					intent.putExtra("id", ids.get(arg2));
					startActivity(intent);
				}
			}
		});

		return rootView;
	}

	/**
	 * Get not voted contest details from database (TABLE - gallery)
	 */
	private void GetContesDetailsFromDatabase() {

		SQLiteDatabase database = null;
		Cursor cursor = null;

		try {

			database = SQLiteDatabase.openDatabase(
					DingDattDatabase.DATABASE_NAME, null,
					SQLiteDatabase.OPEN_READWRITE);

			String url = "SELECT " + DingDattDatabase.FIELD_GALLERY_UPLOADFILE
					+ ", " + DingDattDatabase.FIELD_GALLERY_VOTINGSTATUS + ", "
					+ DingDattDatabase.FIELD_GALLERY_ID + ", "
					+ DingDattDatabase.FIELD_GALLERY_UPLOADTOPIC + " FROM "
					+ DingDattDatabase.TABLE_GALLERY + " WHERE "
					+ DingDattDatabase.FIELD_GALLERY_CONTEST_ID + " = '"
					+ contest_id + "'" + " AND "
					+ DingDattDatabase.FIELD_GALLERY_VOTINGSTATUS + "= ''"
					+ " AND " + DingDattDatabase.FIELD_GALLERY_UPLOADEDSTATUS
					+ "= '" + 0 + "'";

			Log.d("QUERY", url);

			cursor = database.rawQuery(url, null);

			if (cursor != null && cursor.getCount() > 0) {

				cursor.moveToFirst();

				do {
					String topic = "";
					String imgurl = cursor
							.getString(cursor
									.getColumnIndex(DingDattDatabase.FIELD_GALLERY_UPLOADFILE));
					topic = cursor
							.getString(cursor
									.getColumnIndex(DingDattDatabase.FIELD_GALLERY_UPLOADTOPIC));
					topics.add(topic);
					imagesurl.add(imgurl);
					Log.d("url", imgurl);

					String id = cursor.getString(cursor
							.getColumnIndex(DingDattDatabase.FIELD_GALLERY_ID));
					ids.add(id);

				} while (cursor.moveToNext());
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

	/**
	 * Get contest gallery details from server
	 */
	private void GetContestGalleryDetails() {
		try {

			String url = StringURLs.CONTEST_GALLERY;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("contest_id");
			names.add("timezone");
			names.add("user_id");

			LocalData data = new LocalData(context);

			values.add(contest_id);
			values.add(Main.GetTimeZone());
			values.add(data.GetS("userid"));

			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(context);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					UpdateGalleryToGrid(sJSON);
				}
			});

			connectServerImage.setMode(ConnectServerImage.MODE_POST);
			connectServerImage.setNames(names);
			connectServerImage.setValues(values);

			System.out.println(url);

			connectServerImage.execute(url);

		} catch (Exception exp) {

		}
	}

	/**
	 * Update galleries in to grid view
	 * 
	 * @param sJson
	 *            - server response string
	 */
	private void UpdateGalleryToGrid(String sJson) {

		try {
			if (sJson.length() > 0) {

				JSONObject jsonObject = new JSONObject(sJson);

				String success = jsonObject.getJSONObject("response")
						.getString("success");

				if (success.equalsIgnoreCase("1") == true) {

					JSONArray participantlist = jsonObject
							.getJSONArray("participantlist");

					for (int i = 0; i < participantlist.length(); i++) {

						JSONObject object = participantlist.getJSONObject(i);

						/* if(object.getInt("vote") != 0) { */

						ContestGalleryPojo contestGalleryPojo = new ContestGalleryPojo();

						contestGalleryPojo.setContest_id(object
								.getString("contest_id"));
						contestGalleryPojo.setId(object
								.getString("contestparticipantid"));
						contestGalleryPojo.setUploaddate(object
								.getString("uploaddate"));

						imagesurl.add(object.getString("uploadfile"));
						topics.add(object.getString("uploadtopic"));
						ids.add(object.getString("contestparticipantid"));

						contestGalleryPojo.setUploadfile(object
								.getString("uploadfile"));
						contestGalleryPojo.setUploadtopic(object
								.getString("uploadtopic"));
						contestGalleryPojo.setProfilepic(object
								.getString("profilepicture"));
						contestGalleryPojo.setFollowing(object
								.getInt("following"));
						contestGalleryPojo.setName(object.getString("name"));
						contestGalleryPojo.setUser_id(object
								.getString("user_id"));

						contestGalleryPojos.add(contestGalleryPojo);
						/* } */
					}
				} else {
					String msgcode = jsonObject.getJSONObject("response")
							.getString("msgcode");

					Toast.makeText(context,
							Main.getStringResourceByName(context, msgcode),
							Toast.LENGTH_LONG).show();
				}

			} else {
				Toast.makeText(context,
						Main.getStringResourceByName(context, "c100"),
						Toast.LENGTH_LONG).show();
			}

			GalleryAdapter adapter = new GalleryAdapter(context, imagesurl,
					topics, ids, type);
			gridview.setAdapter(adapter);

		} catch (Exception exp) {
			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}
}