package com.bizarre.dingdatt;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONObject;

import com.bizarre.dingdatt.adapter.ContestListAdapter;
import com.bizarre.dingdatt.pojo.ContestDetails;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;

import android.app.LocalActivityManager;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.view.View.OnTouchListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TabHost;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.TabHost.OnTabChangeListener;
import android.widget.TabHost.TabSpec;

/**
 * A placeholder fragment containing a simple view.
 */
public class MyContestFragment extends Fragment {

	private FragmentActivity context;

	private LinearLayout content;
	private View rootView;
	LayoutInflater inflater;
	ListView listView;
	ArrayList<ContestDetails> mContestDetails;
	ArrayList<ContestDetails> mMainContestDetails = new ArrayList<ContestDetails>();

	private Button participatedContest;
	private Button createdContest;

	private String PARTICIPATED_CONTEST = "Participated Contest";
	private String CREATED_CONTEST = "Created Contest";
	private String MY_CONTEST = "mycontest";

	public MyContestFragment(FragmentActivity context) {

		this.context = context;
	}

	public void SearchText(String text) {

		ArrayList<ContestDetails> contestDetails = new ArrayList<ContestDetails>();

		for (int i = 0; i < mMainContestDetails.size(); i++) {
			if (mMainContestDetails.get(i).getContest_name().toLowerCase()
					.contains(text.toLowerCase()) == true) {
				contestDetails.add(mMainContestDetails.get(i));
			}
		}

		mContestDetails = contestDetails;
		UpdateListView();
	}

	@Override
	public View onCreateView(final LayoutInflater inflater1,
			ViewGroup container, Bundle savedInstanceState) {
		this.inflater = inflater1;
		rootView = inflater.inflate(R.layout.my_contest_fragment, container,
				false);

		Main.SetAdvertisment(rootView);

		listView = (ListView) rootView.findViewById(R.id.list);
		listView.setOnItemClickListener(new OnItemClickListener() {

			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int pos,
					long arg3) {
				// TODO Auto-generated method stub

				LocalData data = new LocalData(context);
				ContestDetails contestDetails = mContestDetails.get(pos);

				if (data.GetS(MY_CONTEST)
						.equalsIgnoreCase(PARTICIPATED_CONTEST) == true) {

					Intent intent = new Intent(context,
							ContestInfoSampleActivity.class);
					intent.putExtra("contest_id", contestDetails.getID());
					startActivityForResult(intent, 10);
					context.finish();

				} else if (data.GetS(MY_CONTEST).equalsIgnoreCase(
						CREATED_CONTEST) == true) {

					Intent intent = new Intent(context,
							CreateEditContestActivity.class);
					intent.putExtra("type", 1);
					intent.putExtra("contest_id", contestDetails.getID());
					startActivityForResult(intent, 11);
					context.finish();
				}
			}
		});

		AssignTab(rootView);

		ShowDetails(PARTICIPATED_CONTEST);

		return rootView;
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		// TODO Auto-generated method stub
		super.onActivityResult(requestCode, resultCode, data);

		if (requestCode == 10) {
			ShowDetails(PARTICIPATED_CONTEST);

		} else if (requestCode == 11) {
			ShowDetails(CREATED_CONTEST);
		}
	}

	/**
	 * Assign tab view
	 * 
	 * @param view
	 */
	private void AssignTab(View view) {

		participatedContest = (Button) view.findViewById(R.id.participated);
		createdContest = (Button) view.findViewById(R.id.created);

		participatedContest.setOnTouchListener(new OnTouchListener() {

			@Override
			public boolean onTouch(View v, MotionEvent event) {

				if (event.getAction() == MotionEvent.ACTION_DOWN) {

					participatedContest
							.setBackgroundResource(R.drawable.tab_border_click1);
				} else if (event.getAction() == MotionEvent.ACTION_UP) {

					UpdateTabBackground(v);

					ShowDetails(PARTICIPATED_CONTEST);
				}

				return false;
			}
		});

		createdContest.setOnTouchListener(new OnTouchListener() {

			@Override
			public boolean onTouch(View v, MotionEvent event) {

				if (event.getAction() == MotionEvent.ACTION_DOWN) {

					createdContest
							.setBackgroundResource(R.drawable.tab_border_click1);
				} else if (event.getAction() == MotionEvent.ACTION_UP) {

					UpdateTabBackground(v);

					ShowDetails(CREATED_CONTEST);
				}

				return false;
			}
		});
	}

	/**
	 * 
	 * @param view
	 */
	private void UpdateTabBackground(View view) {

		participatedContest.setBackgroundColor(Color.rgb(115, 219, 255));
		createdContest.setBackgroundColor(Color.rgb(115, 219, 255));

		((TextView) view).setBackgroundResource(R.drawable.tab_border1);
	}

	/**
	 * Show selected tab details
	 * 
	 * @param tabId
	 */
	private void ShowDetails(String tabId) {

		if (tabId.equalsIgnoreCase(PARTICIPATED_CONTEST) == true) {

			LocalData data = new LocalData(context);
			data.Update(MY_CONTEST, PARTICIPATED_CONTEST);
			GetParticipatedContest();

		} else if (tabId.equalsIgnoreCase(CREATED_CONTEST) == true) {

			LocalData data = new LocalData(context);
			data.Update(MY_CONTEST, CREATED_CONTEST);
			GetCreatedContest();
		}
	}

	/**
	 * Get participated contest details.
	 */
	private void GetParticipatedContest() {

		try {

			ArrayList<String> asName = new ArrayList<String>();
			asName.add("userid");
			asName.add("timezone");

			LocalData data = new LocalData(context);

			ArrayList<String> asValue = new ArrayList<String>();
			asValue.add(data.GetS("userid"));
			asValue.add(Main.GetTimeZone());

			String sUrl = StringURLs.PARTICIPATED_CONTEST;

			sUrl = StringURLs.getQuery(sUrl, asName, asValue);

			ConnectServer connectServer = new ConnectServer();
			connectServer.setContext(context);
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub
					try {
						if (sJSON.length() == 0) {

							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();
						} else {
							ShowContestDeatails(sJSON, "participatedcontest");
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

	/**
	 * Show contest details into list
	 * 
	 * @param sJson
	 * @param contest
	 */
	private void ShowContestDeatails(String sJson, String contest) {

		mContestDetails = new ArrayList<ContestDetails>();

		try {

			JSONObject jsonObject = new JSONObject(sJson);
			JSONObject response = jsonObject.getJSONObject("response");
			String success = response.getString("success");

			if (success.equalsIgnoreCase("1") == true) {

				JSONArray participatedcontest = jsonObject
						.getJSONArray(contest);

				for (int i = 0; i < participatedcontest.length(); i++) {

					JSONObject array = participatedcontest.getJSONObject(i);

					ContestDetails contestDetails = new ContestDetails();

					contestDetails.setID(array.getInt("ID"));
					contestDetails.setContest_name(array
							.getString("contest_name"));
					contestDetails.setThemephoto(array.getString("themephoto"));
					contestDetails.setContestenddate(array
							.getString("contestenddate"));
					contestDetails.setConteststartdate(array
							.getString("conteststartdate"));
					contestDetails.setVotingstartdate(array
							.getString("votingstartdate"));
					contestDetails.setVotingenddate(array
							.getString("votingenddate"));
					contestDetails.setPrize(array.getString("prize"));
					contestDetails.setCreatedby(array.getString("createdby"));
					contestDetails.setDescription(array
							.getString("description"));

					if (array.getInt("contestparticipantid") == 1) {

						contestDetails.setContestparticipantid(true);
					} else {

						contestDetails.setContestparticipantid(false);
					}

					mContestDetails.add(contestDetails);
				}
			} else {

				String msgcode = jsonObject.getJSONObject("response")
						.getString("msgcode");

				Toast.makeText(context,
						Main.getStringResourceByName(context, msgcode),
						Toast.LENGTH_LONG).show();
			}

			mMainContestDetails = mContestDetails;

			UpdateListView();

		} catch (Exception exp) {

			Toast.makeText(context,
					Main.getStringResourceByName(context, "c100"),
					Toast.LENGTH_LONG).show();
		}
	}

	/**
	 * Update listview
	 */
	private void UpdateListView() {

		try {

			ContestListAdapter adapter = new ContestListAdapter(context,
					mContestDetails);
			listView.setAdapter(adapter);

		} catch (Exception exp) {

		}
	}

	/**
	 * Get created contest contest details.
	 */
	private void GetCreatedContest() {

		try {

			ArrayList<String> asName = new ArrayList<String>();
			asName.add("userid");
			asName.add("timezone");

			LocalData data = new LocalData(context);

			ArrayList<String> asValue = new ArrayList<String>();
			asValue.add(data.GetS("userid"));
			asValue.add(Main.GetTimeZone());

			String sUrl = StringURLs.CREATED_CONTEST;

			sUrl = StringURLs.getQuery(sUrl, asName, asValue);

			ConnectServer connectServer = new ConnectServer();
			connectServer.setContext(context);
			connectServer.setMode(ConnectServer.MODE_POST);
			connectServer.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub
					try {
						if (sJSON.length() == 0) {
							Toast.makeText(
									context,
									Main.getStringResourceByName(context,
											"c100"), Toast.LENGTH_LONG).show();
						} else {
							ShowContestDeatails(sJSON, "createdcontest");
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