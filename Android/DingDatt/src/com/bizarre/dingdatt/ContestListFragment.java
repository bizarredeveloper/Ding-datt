package com.bizarre.dingdatt;

import java.util.ArrayList;
import org.json.JSONArray;
import org.json.JSONObject;

import com.bizarre.dingdatt.adapter.ContestListAdapter;
import com.bizarre.dingdatt.pojo.ContestDetails;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;

import android.app.Activity;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnTouchListener;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

/**
 * Shows contest list based on contest type
 * 
 * @author karthik
 *
 */
public class ContestListFragment extends Fragment {

	private Button current;
	private Button upcoming;
	private Button archive;
	private Button private1;

	private Button photo;
	private Button video;
	private Button topic;

	ListView list;
	ArrayList<ContestDetails> mContestdetails = new ArrayList<ContestDetails>();
	ArrayList<ContestDetails> mMainContestdetails = new ArrayList<ContestDetails>();
	Activity context;
	String contest_id = "";

	public ContestListFragment(FragmentActivity context, String contest_id) {

		this.context = context;
		this.contest_id = contest_id;
	}

	/**
	 * Filter the contest list
	 * 
	 * @param text
	 *            - filter text
	 */
	public void FilterContestList(String text) {

		ArrayList<ContestDetails> contestDetails = new ArrayList<ContestDetails>();

		for (int i = 0; i < mMainContestdetails.size(); i++) {
			if (mMainContestdetails.get(i).getContest_name().toLowerCase()
					.contains(text.toLowerCase()) == true) {
				contestDetails.add(mMainContestdetails.get(i));
			}
		}

		ContestListAdapter adapter = new ContestListAdapter(context,
				contestDetails);

		mContestdetails = contestDetails;

		list.setAdapter(adapter);
	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		View rootView = inflater.inflate(R.layout.contest_list, container,
				false);

		Main.SetAdvertisment(rootView);

		if (contest_id.equalsIgnoreCase("") == false) {
			CheckContestInfo(contest_id);
		}

		list = (ListView) rootView.findViewById(R.id.list);
		list.setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int pos,
					long arg3) {
				// TODO Auto-generated method stub

				ContestDetails details = mContestdetails.get(pos);

				Intent intent = new Intent(context,
						ContestInfoSampleActivity.class);
				intent.putExtra("contest_details", details);
				startActivity(intent);
			}
		});

		AssignFirstTab(rootView);
		InitSecondTab(rootView);

		LocalData data = new LocalData(context);
		data.Update("clt", "private");
		data.Update("ct", "p");

		GetContestDetails("private", "p");

		return rootView;
	}

	/**
	 * Initiate Second tab
	 * 
	 * @param view
	 */
	private void InitSecondTab(View view) {

		photo = (Button) view.findViewById(R.id.photo);
		video = (Button) view.findViewById(R.id.video);
		topic = (Button) view.findViewById(R.id.topic);

		photo.setOnTouchListener(new OnTouchListener() {

			@Override
			public boolean onTouch(View v, MotionEvent event) {

				if (event.getAction() == MotionEvent.ACTION_DOWN) {

					photo.setBackgroundResource(R.drawable.tab_border_click1);
				} else if (event.getAction() == MotionEvent.ACTION_UP) {

					UpdateSecondTabBackground(v);

					LocalData data = new LocalData(context);
					data.Update("ct", "p");
					GetContestDetails(data.GetS("clt"), data.GetS("ct"));
				}

				return false;
			}
		});

		video.setOnTouchListener(new OnTouchListener() {

			@Override
			public boolean onTouch(View v, MotionEvent event) {

				if (event.getAction() == MotionEvent.ACTION_DOWN) {

					video.setBackgroundResource(R.drawable.tab_border_click1);
				} else if (event.getAction() == MotionEvent.ACTION_UP) {

					UpdateSecondTabBackground(v);

					LocalData data = new LocalData(context);
					data.Update("ct", "v");
					GetContestDetails(data.GetS("clt"), data.GetS("ct"));
				}

				return false;
			}
		});

		topic.setOnTouchListener(new OnTouchListener() {

			@Override
			public boolean onTouch(View v, MotionEvent event) {

				if (event.getAction() == MotionEvent.ACTION_DOWN) {

					topic.setBackgroundResource(R.drawable.tab_border_click1);
				} else if (event.getAction() == MotionEvent.ACTION_UP) {

					UpdateSecondTabBackground(v);

					LocalData data = new LocalData(context);
					data.Update("ct", "t");
					GetContestDetails(data.GetS("clt"), data.GetS("ct"));
				}

				return false;
			}
		});
	}

	/**
	 * 
	 * @param view
	 */
	private void UpdateSecondTabBackground(View view) {

		photo.setBackgroundColor(Color.rgb(115, 219, 255));
		video.setBackgroundColor(Color.rgb(115, 219, 255));
		topic.setBackgroundColor(Color.rgb(115, 219, 255));

		((TextView) view).setBackgroundResource(R.drawable.tab_border1);
	}

	/**
	 * 
	 * @param view
	 */
	private void AssignFirstTab(View view) {
		current = (Button) view.findViewById(R.id.current);
		upcoming = (Button) view.findViewById(R.id.upcoming);
		archive = (Button) view.findViewById(R.id.archive);
		private1 = (Button) view.findViewById(R.id.private1);

		current.setOnTouchListener(new OnTouchListener() {

			@Override
			public boolean onTouch(View v, MotionEvent event) {

				if (event.getAction() == MotionEvent.ACTION_DOWN) {

					current.setBackgroundResource(R.drawable.tab_border_click);
				} else if (event.getAction() == MotionEvent.ACTION_UP) {

					UpdateFirstTabBackground(v);

					LocalData data = new LocalData(context);
					data.Update("clt", "current");
					GetContestDetails(data.GetS("clt"), data.GetS("ct"));
				}

				return false;
			}
		});

		upcoming.setOnTouchListener(new OnTouchListener() {

			@Override
			public boolean onTouch(View v, MotionEvent event) {

				if (event.getAction() == MotionEvent.ACTION_DOWN) {

					upcoming.setBackgroundResource(R.drawable.tab_border_click);
				} else if (event.getAction() == MotionEvent.ACTION_UP) {

					UpdateFirstTabBackground(v);

					LocalData data = new LocalData(context);
					data.Update("clt", "upcoming");
					GetContestDetails(data.GetS("clt"), data.GetS("ct"));
				}

				return false;
			}
		});

		archive.setOnTouchListener(new OnTouchListener() {

			@Override
			public boolean onTouch(View v, MotionEvent event) {

				if (event.getAction() == MotionEvent.ACTION_DOWN) {

					archive.setBackgroundResource(R.drawable.tab_border_click);
				} else if (event.getAction() == MotionEvent.ACTION_UP) {

					UpdateFirstTabBackground(v);

					LocalData data = new LocalData(context);
					data.Update("clt", "archive");
					GetContestDetails(data.GetS("clt"), data.GetS("ct"));
				}

				return false;
			}
		});

		private1.setOnTouchListener(new OnTouchListener() {

			@Override
			public boolean onTouch(View v, MotionEvent event) {

				if (event.getAction() == MotionEvent.ACTION_DOWN) {

					private1.setBackgroundResource(R.drawable.tab_border_click);
				} else if (event.getAction() == MotionEvent.ACTION_UP) {

					UpdateFirstTabBackground(v);

					LocalData data = new LocalData(context);
					data.Update("clt", "private");
					GetContestDetails(data.GetS("clt"), data.GetS("ct"));
				}

				return false;
			}
		});
	}

	/**
	 * 
	 * @param view
	 */
	private void UpdateFirstTabBackground(View view) {

		current.setBackgroundColor(Color.rgb(181, 255, 148));
		upcoming.setBackgroundColor(Color.rgb(181, 255, 148));
		private1.setBackgroundColor(Color.rgb(181, 255, 148));
		archive.setBackgroundColor(Color.rgb(181, 255, 148));

		((TextView) view).setBackgroundResource(R.drawable.tab_border);
	}

	/**
	 * Get contest details from server.
	 * 
	 * @param contestlisttype
	 *            - private, upcoming, achieve or current
	 * @param contesttype
	 *            - photo, video or topic
	 */
	private void GetContestDetails(String contestlisttype, String contesttype) {

		try {

			ArrayList<String> asName = new ArrayList<String>();
			ArrayList<String> asValue = new ArrayList<String>();
			LocalData data = new LocalData(context);

			asName.add("contestlisttype");
			asName.add("contesttype");
			asName.add("loggeduserid");
			asName.add("timezone");

			asValue.add(contestlisttype);
			asValue.add(contesttype);
			asValue.add(data.GetS("userid"));
			asValue.add(Main.GetTimeZone());

			String sURL = StringURLs.CONTEST_LIST;

			sURL = StringURLs.getQuery(sURL, asName, asValue);

			ConnectServer connectServer = new ConnectServer();
			connectServer.setContext(context);
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					if (sJSON.length() > 0) {

						mContestdetails = new ArrayList<ContestDetails>();
						UpdateJSONDataToList(sJSON);
						Log.d("json", "json " + sJSON);
						mMainContestdetails = mContestdetails;
						ContestListAdapter adapter = new ContestListAdapter(
								context, mContestdetails);
						list.setAdapter(adapter);

					} else {

						Toast.makeText(context,
								Main.getStringResourceByName(context, "c100"),
								Toast.LENGTH_LONG).show();
					}
				}
			});

			connectServer.execute(sURL);
		} catch (Exception exp) {

		}
	}

	/**
	 * 
	 * @param sContID
	 */
	private void CheckContestInfo(String sContID) {

		ArrayList<String> asName = new ArrayList<String>();
		asName.add("contestid");
		asName.add("user_id");
		asName.add("timezone");

		ArrayList<String> asValue = new ArrayList<String>();
		asValue.add(sContID);

		LocalData data = new LocalData(context);
		asValue.add(data.GetS("userid"));
		asValue.add(Main.GetTimeZone());

		String sUrl = "";

		try {
			sUrl = StringURLs
					.getQuery(StringURLs.CONTEST_INFO, asName, asValue);
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		ConnectServer connectServer = new ConnectServer();
		connectServer.setMode(ConnectServer.MODE_POST);
		connectServer.setContext(context);
		connectServer.setListener(new ConnectServerListener() {

			@Override
			public void onServerResponse(String sJSON, JSONObject jsonObject) {
				// TODO Auto-generated method stub

				if (sJSON.length() == 0) {

					Toast.makeText(context,
							Main.getStringResourceByName(context, "c100"),
							Toast.LENGTH_LONG).show();

				} else {

					try {
						JSONObject object = new JSONObject(sJSON);

						JSONObject response = object.getJSONObject("response");

						String success = response.getString("success");
						String message = response.getString("message");

						if (success.equalsIgnoreCase("1") == true) {
							Intent intent = new Intent(context,
									ContestInfoSampleActivity.class);
							intent.putExtra("contest_id",
									Integer.parseInt(contest_id));
							startActivity(intent);
							context.finish();
						} else {
							String msgcode = jsonObject.getJSONObject(
									"response").getString("msgcode");

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											msgcode), Toast.LENGTH_LONG).show();
						}
					} catch (Exception exp) {

						Toast.makeText(context,
								Main.getStringResourceByName(context, "c100"),
								Toast.LENGTH_LONG).show();
					}
				}
			}
		});

		connectServer.execute(sUrl);
	}

	/**
	 * Update JSON data to listview
	 * 
	 * @param sJSON
	 */
	private void UpdateJSONDataToList(String sJSON) {

		try {

			JSONObject jsonObject = new JSONObject(sJSON);

			JSONObject response = jsonObject.getJSONObject("response");

			String success = response.getString("success");

			if (success.equalsIgnoreCase("1")) {

				JSONArray contestdetails = jsonObject
						.getJSONArray("contestdetails");

				for (int i = 0; i < contestdetails.length(); i++) {

					JSONObject contestdetailsobj = contestdetails
							.getJSONObject(i);
					ContestDetails contestdetails2 = new ContestDetails();

					contestdetails2.setID(contestdetailsobj.getInt("ID"));
					contestdetails2.setContest_name(contestdetailsobj
							.getString("contest_name"));
					contestdetails2.setDescription(contestdetailsobj
							.getString("description"));

					contestdetails2.setConteststartdate(contestdetailsobj
							.getString("conteststartdate"));
					contestdetails2.setContestenddate(contestdetailsobj
							.getString("contestenddate"));

					contestdetails2.setVotingstartdate(contestdetailsobj
							.getString("votingstartdate"));
					contestdetails2.setVotingenddate(contestdetailsobj
							.getString("votingenddate"));

					contestdetails2.setPrize(contestdetailsobj
							.getString("prize"));

					if (contestdetailsobj.getInt("contestparticipantid") == 1)
						contestdetails2.setContestparticipantid(true);
					else
						contestdetails2.setContestparticipantid(false);

					contestdetails2.setThemephoto(contestdetailsobj
							.getString("themephoto"));

					mContestdetails.add(contestdetails2);
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
}
