package com.bizarre.dingdatt;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONObject;

import com.bizarre.dingdatt.adapter.CommentReplyClick;
import com.bizarre.dingdatt.adapter.CommentsAdapter;
import com.bizarre.dingdatt.imageloader.ImageLoader;
import com.bizarre.dingdatt.pojo.CommentsPojo;
import com.bizarre.dingdatt.strings.LocalData;
import com.bizarre.dingdatt.strings.StringURLs;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
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
 * This is used to shows the comments for participated contest
 * 
 * @author karthik
 *
 */
public class CommentFragment extends Fragment {

	private FragmentActivity mobjContext;
	private View mrootView;
	LayoutInflater inflater;

	LinearLayout mlayoutParticipantView;
	ImageView mimgCreatorImage;
	TextView mtxtCreatorName;

	ImageView mimgFollowbell;
	LinearLayout mlayoutComments;
	LinearLayout mlayoutSubcomments;
	ImageView subprofile;
	TextView subuser;
	ImageView back;
	LinearLayout subcomments1;

	EditText comment;
	Button follow;
	Button submit;

	String contest_id = "";
	String name = "";
	String id = "";
	String type = "";
	String value = "";
	String participantid = "";

	ArrayList<String> asids = new ArrayList<String>();
	ArrayList<String> acommentid = new ArrayList<String>();
	ArrayList<String> anames = new ArrayList<String>();
	ArrayList<String> aprofilepic = new ArrayList<String>();
	ArrayList<String> acomments = new ArrayList<String>();

	ArrayList<CommentsPojo> commentsPojos = new ArrayList<CommentsPojo>();
	ArrayList<CommentsPojo> replycommentsPojos = new ArrayList<CommentsPojo>();

	/**
	 * 
	 * @param context
	 * @param contest_id
	 * @param name
	 * @param id
	 * @param type
	 * @param value
	 * @param participantid
	 */
	public CommentFragment(FragmentActivity context, String contest_id,
			String name, String id, String type, String value,
			String participantid) {

		this.mobjContext = context;
		this.contest_id = contest_id;
		this.name = name;
		this.id = id;
		this.type = type;
		this.value = value;
		this.participantid = participantid;
	}

	private static int MAIN_COMMENT = 0;
	private static int SUB_COMMENT = 1;

	@Override
	public View onCreateView(final LayoutInflater inflater1,
			ViewGroup container, Bundle savedInstanceState) {
		this.inflater = inflater1;
		mrootView = inflater.inflate(R.layout.comment, container, false);

		Main.SetAdvertisment(mrootView);

		mlayoutParticipantView = (LinearLayout) mrootView
				.findViewById(R.id.image);
		follow = (Button) mrootView.findViewById(R.id.follow);

		follow.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				Follow(v.getTag().toString());
			}
		});

		if (type.equalsIgnoreCase("p") == true) {

			ImageView imageView = new ImageView(mobjContext);
			imageView.setLayoutParams(new LayoutParams(
					LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT));

			int loader = R.drawable.loader;
			ImageLoader imageLoader = new ImageLoader(mobjContext);
			imageLoader.DisplayImage(value, loader, imageView);
			mlayoutParticipantView.addView(imageView);

		} else if (type.equalsIgnoreCase("v") == true) {
			MediaController mediaControls = new MediaController(mobjContext);

			VideoView video = new VideoView(mobjContext);
			video.setLayoutParams(new LayoutParams(LayoutParams.MATCH_PARENT,
					LayoutParams.MATCH_PARENT));

			video.setMediaController(mediaControls);
			video.setVideoURI(Uri.parse(value));
			video.start();
			mlayoutParticipantView.addView(video);

		} else if (type.equalsIgnoreCase("t") == true) {

			TextView textView = new TextView(mobjContext);
			textView.setLayoutParams(new LayoutParams(
					LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT));

			textView.setText(value);
			mlayoutParticipantView.addView(textView);
		}

		mimgCreatorImage = (ImageView) mrootView
				.findViewById(R.id.creatorimage);
		mtxtCreatorName = (TextView) mrootView.findViewById(R.id.creatorname);
		mimgFollowbell = (ImageView) mrootView.findViewById(R.id.followbell);
		mlayoutComments = (LinearLayout) mrootView.findViewById(R.id.comments);

		back = (ImageView) mrootView.findViewById(R.id.back);

		mlayoutSubcomments = (LinearLayout) mrootView
				.findViewById(R.id.subcomments);

		subprofile = (ImageView) mrootView.findViewById(R.id.subprofile);
		subuser = (TextView) mrootView.findViewById(R.id.subuser);

		subcomments1 = (LinearLayout) mrootView.findViewById(R.id.subcomments1);
		comment = (EditText) mrootView.findViewById(R.id.comment);
		submit = (Button) mrootView.findViewById(R.id.submit);

		submit.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (mlayoutComments.getVisibility() == View.VISIBLE) {

					PostComments(MAIN_COMMENT);

				} else {

					PostComments(SUB_COMMENT);

				}
			}
		});

		back.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				mlayoutComments.setVisibility(View.VISIBLE);
				mlayoutSubcomments.setVisibility(View.GONE);
				comment.setText("");
			}
		});

		GetComments();

		return mrootView;
	}

	/**
	 * Login user follows to given userid.
	 * 
	 * @param sUserid
	 *            -
	 */
	private void Follow(String sUserid) {

		String url = StringURLs.FOLLOWER;

		ArrayList<String> names = new ArrayList<String>();
		ArrayList<String> values = new ArrayList<String>();

		names.add("followerid");
		names.add("timezone");
		names.add("userid");

		LocalData data = new LocalData(mobjContext);
		values.add(data.GetS("userid"));
		values.add(Main.GetTimeZone());
		values.add(sUserid);

		ConnectServerImage connectServerImage = new ConnectServerImage();
		connectServerImage.setContext(mobjContext);
		connectServerImage.setListener(new ConnectServerListener() {

			@Override
			public void onServerResponse(String sJSON, JSONObject jsonObject) {

				try {

					if (sJSON.length() > 0) {

						JSONObject object = new JSONObject(sJSON);
						String success = object.getJSONObject("response")
								.getString("success");

						if (success.equalsIgnoreCase("1") == true) {
							String msgcode = jsonObject.getJSONObject(
									"response").getString("msgcode");

							Toast.makeText(
									mobjContext,
									Main.getStringResourceByName(mobjContext,
											msgcode), Toast.LENGTH_LONG).show();

							follow.setVisibility(View.GONE);
							int imgResource = R.drawable.bell_green;
							mtxtCreatorName
									.setCompoundDrawablesWithIntrinsicBounds(0,
											0, imgResource, 0);

						} else {
							follow.setVisibility(View.VISIBLE);
							String msgcode = jsonObject.getJSONObject(
									"response").getString("msgcode");

							Toast.makeText(
									mobjContext,
									Main.getStringResourceByName(mobjContext,
											msgcode), Toast.LENGTH_LONG).show();
						}

					} else {
						follow.setVisibility(View.VISIBLE);
						Toast.makeText(
								mobjContext,
								Main.getStringResourceByName(mobjContext,
										"c100"), Toast.LENGTH_LONG).show();
					}

				} catch (Exception exp) {
					follow.setVisibility(View.VISIBLE);
					Toast.makeText(mobjContext,
							Main.getStringResourceByName(mobjContext, "c100"),
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
	 * Call service to post comments
	 * 
	 * @param type
	 *            - MAIN_COMMENT, SUB_COMMENT
	 */
	private void PostComments(int type) {

		String url = "";
		ArrayList<String> names = new ArrayList<String>();
		ArrayList<String> values = new ArrayList<String>();

		if (type == MAIN_COMMENT) {
			url = StringURLs.COMMENTS;

			names.add("contest_participant_id");
			names.add("timezone");
			names.add("userid");
			names.add("comment");

			LocalData data = new LocalData(mobjContext);
			values.add(id);
			values.add(Main.GetTimeZone());
			values.add(data.GetS("userid"));
			values.add(comment.getText().toString().trim());

		} else if (type == SUB_COMMENT) {
			url = StringURLs.REPLYCOMMENTS;

			names.add("comment_id");
			names.add("timezone");
			names.add("user_id");
			names.add("replycomment");

			LocalData data = new LocalData(mobjContext);
			values.add(acommentid.get(Integer.parseInt(comment.getTag()
					.toString())));
			values.add(Main.GetTimeZone());
			values.add(data.GetS("userid"));
			values.add(comment.getText().toString().trim());
		}

		ConnectServerImage connectServerImage = new ConnectServerImage();
		connectServerImage.setContext(mobjContext);
		connectServerImage.setListener(new ConnectServerListener() {

			@Override
			public void onServerResponse(String sJSON, JSONObject jsonObject) {
				// TODO Auto-generated method stub

				try {

					JSONObject jsonObject2 = new JSONObject(sJSON);

					if (jsonObject2.getJSONObject("response")
							.getString("success").equalsIgnoreCase("1") == true) {

						Toast.makeText(mobjContext, "Successfully added.",
								Toast.LENGTH_LONG).show();

						comment.setText("");

						if (mlayoutComments.getVisibility() == View.VISIBLE) {

							GetComments();

						} else {

							GetSubComments(Integer.parseInt(comment.getTag()
									.toString()));
						}

					} else {

						String msgcode = jsonObject2.getJSONObject("response")
								.getString("msgcode");

						Toast.makeText(
								mobjContext,
								Main.getStringResourceByName(mobjContext,
										msgcode), Toast.LENGTH_LONG).show();
					}

				} catch (Exception exp) {

					Toast.makeText(mobjContext,
							Main.getStringResourceByName(mobjContext, "c100"),
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
	 * Call service to get comments
	 */
	private void GetComments() {

		String url = StringURLs.GETCOMMENTS;

		ArrayList<String> names = new ArrayList<String>();
		ArrayList<String> values = new ArrayList<String>();

		names.add("contest_participant_id");
		names.add("timezone");
		names.add("userid");

		LocalData data = new LocalData(mobjContext);

		values.add(id);
		values.add(Main.GetTimeZone());
		values.add(data.GetS("userid"));

		ConnectServerImage connectServerImage = new ConnectServerImage();
		connectServerImage.setContext(mobjContext);
		connectServerImage.setListener(new ConnectServerListener() {

			@Override
			public void onServerResponse(String sJSON, JSONObject jsonObject) {
				// TODO Auto-generated method stub
				try {

					if (sJSON.length() > 0) {
						commentsPojos = new ArrayList<CommentsPojo>();

						asids = new ArrayList<String>();
						acommentid = new ArrayList<String>();
						anames = new ArrayList<String>();
						aprofilepic = new ArrayList<String>();
						acomments = new ArrayList<String>();

						JSONObject jsonObject2 = new JSONObject(sJSON);

						if (jsonObject2.getJSONObject("response")
								.getString("success").equalsIgnoreCase("1") == true) {

							JSONArray Comments = jsonObject2
									.getJSONArray("Comments");

							for (int i = 0; i < Comments.length(); i++) {

								JSONObject object = Comments.getJSONObject(i);

								CommentsPojo commentsPojo = new CommentsPojo();

								commentsPojo.setComment_id(object
										.getString("comment_id"));
								commentsPojo.setContest_participant_id(object
										.getString("contest_participant_id"));
								commentsPojo.setComment(object
										.getString("comment"));
								commentsPojo.setUserid(object
										.getString("userid"));
								commentsPojo.setFirstname(object
										.getString("firstname"));
								commentsPojo.setProfilepicture(object
										.getString("profilepicture"));

								anames.add(object.getString("name"));
								aprofilepic.add(object
										.getString("profilepicture"));
								acomments.add(object.getString("comment"));
								asids.add(object
										.getString("contest_participant_id"));
								acommentid.add(object.getString("comment_id"));

								commentsPojos.add(commentsPojo);
							}

							int followers = jsonObject2.getInt("followers");

							if (followers == 0) {

								mimgFollowbell.setVisibility(View.GONE);

								LocalData data = new LocalData(mobjContext);

								if (data.GetS("userid").equalsIgnoreCase(
										participantid) == true)
									follow.setVisibility(View.GONE);
								else
									follow.setVisibility(View.VISIBLE);

								follow.setTag(participantid);
							} else {
								mimgFollowbell.setVisibility(View.VISIBLE);
								follow.setVisibility(View.GONE);
								follow.setTag("");
							}

							GetContestParticipantInfo();

						} else {
							String msgcode = jsonObject2.getJSONObject(
									"response").getString("msgcode");

							Toast.makeText(
									mobjContext,
									Main.getStringResourceByName(mobjContext,
											msgcode), Toast.LENGTH_LONG).show();

							int followers = jsonObject2.getInt("followers");

							if (followers == 0) {

								mimgFollowbell.setVisibility(View.GONE);

								LocalData data = new LocalData(mobjContext);

								if (data.GetS("userid").equalsIgnoreCase(
										participantid) == true)
									follow.setVisibility(View.GONE);
								else
									follow.setVisibility(View.VISIBLE);

								follow.setTag(participantid);

							} else {
								mimgFollowbell.setVisibility(View.VISIBLE);
								follow.setVisibility(View.GONE);

								follow.setTag("");
							}

							GetContestParticipantInfo();
						}

					} else {

						Toast.makeText(
								mobjContext,
								Main.getStringResourceByName(mobjContext,
										"c100"), Toast.LENGTH_LONG).show();
					}

				} catch (Exception exp) {

					Toast.makeText(mobjContext,
							Main.getStringResourceByName(mobjContext, "c100"),
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
	 * Get contest participated user details
	 */
	private void GetContestParticipantInfo() {

		try {

			String url = StringURLs.PARTICIPANT_DETAILS;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("contest_id");
			names.add("participantuserid");
			names.add("timezone");

			values.add(contest_id);
			values.add(participantid);
			values.add(Main.GetTimeZone());

			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(mobjContext);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {

					try {

						JSONObject jsonObject2 = new JSONObject(sJSON);

						if (jsonObject2.getJSONObject("response")
								.getString("success").equalsIgnoreCase("1") == true) {

							JSONArray contestdetails = jsonObject2
									.getJSONArray("participantlist");

							for (int i = 0; i < contestdetails.length(); i++) {

								JSONObject jsonObject3 = contestdetails
										.getJSONObject(i);
								String name = jsonObject3.getString("name");
								String profilepics = jsonObject3
										.getString("profilepicture");

								int loader = R.drawable.avator;
								ImageLoader imageLoader = new ImageLoader(
										mobjContext);
								imageLoader.DisplayImage(profilepics, loader,
										mimgCreatorImage);

								mtxtCreatorName.setText(name);
							}

						} else {

							String msgcode = jsonObject2.getJSONObject(
									"response").getString("msgcode");

							Toast.makeText(
									mobjContext,
									Main.getStringResourceByName(mobjContext,
											msgcode), Toast.LENGTH_LONG).show();
						}

						AddandUpdateComments();

					} catch (Exception exp) {

						Toast.makeText(
								mobjContext,
								Main.getStringResourceByName(mobjContext,
										"c100"), Toast.LENGTH_LONG).show();
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
	 * Add and Update comments into screen.
	 */
	private void AddandUpdateComments() {

		try {
			mlayoutComments.setVisibility(View.VISIBLE);
			mlayoutSubcomments.setVisibility(View.GONE);

			CommentsAdapter adapter = new CommentsAdapter(mobjContext,
					aprofilepic, asids, acomments, anames, acommentid,
					new CommentReplyClick() {

						@Override
						public void onReplyClick(int iPos) {

							// TODO Auto-generated method stub
							mlayoutComments.setVisibility(View.GONE);
							mlayoutSubcomments.setVisibility(View.VISIBLE);
							subcomments1.removeAllViews();
							comment.setText("");
							comment.setTag(iPos + "");

							String pic = aprofilepic.get(iPos);
							String name = anames.get(iPos);

							int loader = R.drawable.avator;
							ImageLoader imageLoader = new ImageLoader(
									mobjContext);
							imageLoader.DisplayImage(pic, loader, subprofile);

							subuser.setText(name);
							GetSubComments(iPos);
						}
					});

			final int adapterCount = adapter.getCount();

			mlayoutComments.removeAllViews();

			for (int i = 0; i < adapterCount; i++) {
				View item = adapter.getView(i, null, null);
				mlayoutComments.addView(item);
			}

		} catch (Exception exp) {

		}
	}

	/**
	 * Get sub comments for selected comments
	 * 
	 * @param iPos
	 *            - selected commet position
	 */
	private void GetSubComments(int iPos) {

		try {

			String url = StringURLs.GETREPLYCOMMENTS;

			ArrayList<String> names = new ArrayList<String>();
			ArrayList<String> values = new ArrayList<String>();

			names.add("user_id");
			names.add("comment_id");

			LocalData data = new LocalData(mobjContext);
			String comment_id = acommentid.get(iPos);

			values.add(data.GetS("userid"));
			values.add(comment_id);

			ConnectServerImage connectServerImage = new ConnectServerImage();
			connectServerImage.setContext(mobjContext);
			connectServerImage.setListener(new ConnectServerListener() {

				@Override
				public void onServerResponse(String sJSON, JSONObject jsonObject) {
					// TODO Auto-generated method stub

					try {

						replycommentsPojos = new ArrayList<CommentsPojo>();

						if (sJSON.length() > 0) {

							JSONObject jsonObject2 = new JSONObject(sJSON);

							if (jsonObject2.getJSONObject("response")
									.getString("success").equalsIgnoreCase("1") == true) {

								JSONArray replydata = jsonObject2
										.getJSONArray("replydata");

								for (int i = 0; i < replydata.length(); i++) {

									JSONObject rpd = replydata.getJSONObject(i);

									CommentsPojo commentsPojo = new CommentsPojo();
									commentsPojo.setComment(rpd
											.getString("replycomment"));
									// commentsPojo.setComment_id(rpd.getString("id"));
									commentsPojo.setContest_participant_id(rpd
											.getString("comment_id"));
									commentsPojo.setFirstname(rpd
											.getString("name"));
									commentsPojo.setProfilepicture(rpd
											.getString("profilepicture"));
									commentsPojo.setUserid(rpd
											.getString("user_id"));

									replycommentsPojos.add(commentsPojo);
								}

							} else {
								String msgcode = jsonObject2.getJSONObject(
										"response").getString("msgcode");

								Toast.makeText(
										mobjContext,
										Main.getStringResourceByName(
												mobjContext, msgcode),
										Toast.LENGTH_LONG).show();
							}

						} else {

							Toast.makeText(
									mobjContext,
									Main.getStringResourceByName(mobjContext,
											"c100"), Toast.LENGTH_LONG).show();
						}

						AddandUpdateSubComments();

					} catch (Exception exp) {

						Toast.makeText(
								mobjContext,
								Main.getStringResourceByName(mobjContext,
										"c100"), Toast.LENGTH_LONG).show();
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
	 * Add and Update sub comments into screen.
	 */
	private void AddandUpdateSubComments() {

		ArrayList<String> profilepic = new ArrayList<String>();
		ArrayList<String> ids = new ArrayList<String>();
		ArrayList<String> comments1 = new ArrayList<String>();
		ArrayList<String> names = new ArrayList<String>();
		ArrayList<String> commentid = new ArrayList<String>();

		for (int i = 0; i < replycommentsPojos.size(); i++) {
			profilepic.add(replycommentsPojos.get(i).getProfilepicture());
			ids.add(i + "");
			comments1.add(replycommentsPojos.get(i).getComment());
			names.add(replycommentsPojos.get(i).getFirstname());
			commentid.add(replycommentsPojos.get(i).getComment_id());
		}

		CommentsAdapter adapter = new CommentsAdapter(mobjContext, profilepic,
				ids, comments1, names, commentid, null);

		final int adapterCount = adapter.getCount();

		for (int i = 0; i < adapterCount; i++) {
			View item = adapter.getView(i, null, null);
			subcomments1.addView(item);
		}

	}
}