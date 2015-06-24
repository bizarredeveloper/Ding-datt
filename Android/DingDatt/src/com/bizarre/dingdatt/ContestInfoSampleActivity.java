package com.bizarre.dingdatt;

import java.util.Arrays;

import com.bizarre.dingdatt.pojo.ContestDetails;
import com.bizarre.dingdatt.strings.CommonConfig;
import com.bizarre.dingdatt.strings.LocalData;
import com.facebook.AppEventsLogger;
import com.facebook.FacebookAuthorizationException;
import com.facebook.FacebookOperationCanceledException;
import com.facebook.FacebookRequestError;
import com.facebook.Request;
import com.facebook.Response;
import com.facebook.Session;
import com.facebook.SessionState;
import com.facebook.UiLifecycleHelper;
import com.facebook.model.GraphObject;
import com.facebook.widget.FacebookDialog;
import com.facebook.widget.LoginButton;

import android.app.ActionBar;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.Intent;
import android.content.res.Configuration;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.Bundle;
import android.support.v4.app.ActionBarDrawerToggle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.ListView;

public class ContestInfoSampleActivity extends FragmentActivity {
	private DrawerLayout mDrawerLayout;
	private ListView mDrawerList;
	private ActionBarDrawerToggle mDrawerToggle;

	private CharSequence mDrawerTitle;
	private CharSequence mTitle;
	private String[] mPlanetTitles;
	ImageView imageView122;
	ContestDetails contestDetails;
	int contest_id = 0;
	public static Activity info;

	public ContestInfoSampleActivity() {

	}

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_contest_list_sample);

		info = this;
		uiHelper = new UiLifecycleHelper(this, callback);
		uiHelper.onCreate(savedInstanceState);

		Bundle bundle = getIntent().getExtras();

		if (bundle != null) {
			contest_id = bundle.getInt("contest_id", 0);

			if (contest_id == 0) {
				contestDetails = (ContestDetails) getIntent()
						.getSerializableExtra("contest_details");
				contest_id = contestDetails.getID();
			}

			LocalData data = new LocalData(getApplicationContext());
			data.Update("contest_id", "");
		}

		mTitle = mDrawerTitle = getTitle();
		mPlanetTitles = getResources().getStringArray(R.array.menu_list);
		mDrawerLayout = (DrawerLayout) findViewById(R.id.drawer_layout);
		mDrawerList = (ListView) findViewById(R.id.left_drawer);

		// set a custom shadow that overlays the main content when the drawer
		// opens
		mDrawerLayout.setDrawerShadow(R.drawable.drawer_shadow,
				GravityCompat.START);
		// set up the drawer's list view with items and click listener

		mPlanetTitles = Main.UpdateDrawerlist(this, mPlanetTitles);

		mDrawerList.setAdapter(new ArrayAdapter<String>(this,
				R.layout.drawer_list_item, mPlanetTitles));
		mDrawerList.setOnItemClickListener(new DrawerItemClickListener());

		// enable ActionBar app icon to behave as action to toggle nav drawer
		getActionBar().setDisplayHomeAsUpEnabled(true);
		getActionBar().setHomeButtonEnabled(true);

		// ActionBarDrawerToggle ties together the the proper interactions
		// between the sliding drawer and the action bar app icon
		mDrawerToggle = new ActionBarDrawerToggle(this, /* host Activity */
		mDrawerLayout, /* DrawerLayout object */
		R.drawable.ic_drawer, /* nav drawer image to replace 'Up' caret */
		R.string.open, /* "open drawer" description for accessibility */
		R.string.close /* "close drawer" description for accessibility */
		) {
			public void onDrawerClosed(View view) {
				getActionBar().setTitle(mTitle);
				invalidateOptionsMenu(); // creates call to
											// onPrepareOptionsMenu()
			}

			public void onDrawerOpened(View drawerView) {
				getActionBar().setTitle(mDrawerTitle);
				invalidateOptionsMenu(); // creates call to
											// onPrepareOptionsMenu()
			}
		};

		mDrawerLayout.setDrawerListener(mDrawerToggle);
		if (savedInstanceState == null) {

			// update the main content by replacing fragments
			getSupportFragmentManager()
					.beginTransaction()
					.replace(R.id.content_frame,
							new ContestInfoFragment(this, contest_id)).commit();
		}

		getActionBar().setDisplayOptions(ActionBar.DISPLAY_SHOW_CUSTOM);
		getActionBar().setCustomView(R.layout.abs_layout);

		imageView122 = (ImageView) getActionBar().getCustomView().findViewById(
				R.id.menudrawer);

		Main.SetTitle(getActionBar().getCustomView(), CommonConfig.CONTEST_INFO);

		imageView122.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				if (mDrawerLayout.isDrawerVisible(Gravity.LEFT)) {
					mDrawerLayout.closeDrawers();
				} else {
					mDrawerLayout.openDrawer(Gravity.LEFT);
				}
			}
		});
	}

	private class DrawerItemClickListener implements
			ListView.OnItemClickListener {
		@Override
		public void onItemClick(AdapterView<?> parent, View view, int position,
				long id) {

			selectItem(position);
		}
	}

	private void selectItem(int position) {

		Main.MenuList(this, position);

		mDrawerList.setItemChecked(position, true);
		setTitle(mPlanetTitles[position]);
		mDrawerLayout.closeDrawer(mDrawerList);
	}

	@Override
	public void setTitle(CharSequence title) {
		mTitle = title;
		getActionBar().setTitle(mTitle);
	}

	/**
	 * When using the ActionBarDrawerToggle, you must call it during
	 * onPostCreate() and onConfigurationChanged()...
	 */

	@Override
	protected void onPostCreate(Bundle savedInstanceState) {
		super.onPostCreate(savedInstanceState);
		// Sync the toggle state after onRestoreInstanceState has occurred.
		mDrawerToggle.syncState();
	}

	@Override
	public void onConfigurationChanged(Configuration newConfig) {
		super.onConfigurationChanged(newConfig);
		// Pass any configuration change to the drawer toggls
		mDrawerToggle.onConfigurationChanged(newConfig);
	}

	/****
	 * Facbook code
	 */

	private UiLifecycleHelper uiHelper;

	private Session.StatusCallback callback = new Session.StatusCallback() {
		@Override
		public void call(Session session, SessionState state,
				Exception exception) {
			onSessionStateChange(session, state, exception);
		}
	};

	private void onSessionStateChange(Session session, SessionState state,
			Exception exception) {
		if (pendingAction != PendingAction.NONE
				&& (exception instanceof FacebookOperationCanceledException || exception instanceof FacebookAuthorizationException)) {
			new AlertDialog.Builder(this).setTitle("Cancelled")
					.setMessage("Permission not granted,")
					.setPositiveButton("OK", null).show();
			pendingAction = PendingAction.NONE;
		} else if (state == SessionState.OPENED_TOKEN_UPDATED) {
			handlePendingAction();
		}

		updateUI();
	}

	private boolean updateUI() {
		Session session = Session.getActiveSession();
		return (session != null && session.isOpened());
	}

	LoginButton fb2;

	@Override
	public void onResume() {
		super.onResume();
		uiHelper.onResume();

		// Call the 'activateApp' method to log an app event for use in
		// analytics and advertising reporting. Do so in
		// the onResume methods of the primary Activities that an app may be
		// launched into.
		AppEventsLogger.activateApp(this);

		updateUI();
	}

	@Override
	public void onSaveInstanceState(Bundle outState) {
		super.onSaveInstanceState(outState);
		uiHelper.onSaveInstanceState(outState);

		outState.putString(PENDING_ACTION_BUNDLE_KEY, pendingAction.name());
	}

	private FacebookDialog.Callback dialogCallback = new FacebookDialog.Callback() {
		@Override
		public void onError(FacebookDialog.PendingCall pendingCall,
				Exception error, Bundle data) {
			Log.d("HelloFacebook", String.format("Error: %s", error.toString()));
		}

		@Override
		public void onComplete(FacebookDialog.PendingCall pendingCall,
				Bundle data) {
			Log.d("HelloFacebook", "Success!");
		}
	};

	@Override
	public void onPause() {
		super.onPause();
		uiHelper.onPause();

		// Call the 'deactivateApp' method to log an app event for use in
		// analytics and advertising
		// reporting. Do so in the onPause methods of the primary Activities
		// that an app may be launched into.
		AppEventsLogger.deactivateApp(this);
	}

	@Override
	public void onDestroy() {
		super.onDestroy();
		uiHelper.onDestroy();
	}

	private enum PendingAction {
		NONE, POST_PHOTO, POST_STATUS_UPDATE
	}

	private boolean canPresentShareDialogWithPhotos;
	private PendingAction pendingAction = PendingAction.NONE;
	private static final String PERMISSION = "publish_actions";
	private final String PENDING_ACTION_BUNDLE_KEY = "com.bizarre.dingdatt:PendingAction";

	public void onClickPostPhoto() {
		performPublish(PendingAction.POST_PHOTO,
				canPresentShareDialogWithPhotos);
	}

	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		// TODO Auto-generated method stub
		super.onActivityResult(requestCode, resultCode, data);

		uiHelper.onActivityResult(requestCode, resultCode, data, dialogCallback);

		/*
		 * Session session = Session.getActiveSession();
		 * 
		 * if (session != null && session.isOpened()) {
		 * 
		 * // Check for publish permissions List<String> permissions =
		 * session.getPermissions(); if (!isSubsetOf(PERMISSIONS, permissions))
		 * { pendingPublishReauthorization = true; Session.NewPermissionsRequest
		 * newPermissionsRequest = new Session.NewPermissionsRequest( this,
		 * PERMISSIONS);
		 * session.requestNewPublishPermissions(newPermissionsRequest); return;
		 * } }
		 */
	}

	/*
	 * private static final List<String> PERMISSIONS =
	 * Arrays.asList("publish_stream, publish_actions"); private boolean
	 * pendingPublishReauthorization = false;
	 * 
	 * private boolean isSubsetOf(Collection<String> subset, Collection<String>
	 * superset) { for (String string : subset) { if
	 * (!superset.contains(string)) { return false; } } return true; }
	 */

	private void performPublish(PendingAction action, boolean allowNoSession) {
		Session session = Session.getActiveSession();
		if (session != null) {
			pendingAction = action;
			if (hasPublishPermission()) {
				// We can do the action right away.
				handlePendingAction();
				return;
			} else if (session.isOpened()) {
				// We need to get new permissions, then complete the action when
				// we get called back.
				session.requestNewPublishPermissions(new Session.NewPermissionsRequest(
						this, PERMISSION));
				return;
			}
		}

		if (allowNoSession) {
			pendingAction = action;
			handlePendingAction();
		}
	}

	private boolean hasPublishPermission() {
		Session session = Session.getActiveSession();
		return session != null
				&& session.getPermissions().contains("publish_actions");
	}

	@SuppressWarnings("incomplete-switch")
	private void handlePendingAction() {
		PendingAction previouslyPendingAction = pendingAction;
		// These actions may re-set pendingAction if they are still pending, but
		// we assume they
		// will succeed.
		pendingAction = PendingAction.NONE;

		switch (previouslyPendingAction) {
		case POST_PHOTO:
			postPhoto();
			break;
		}
	}

	private void postPhoto() {
		Bitmap image = BitmapFactory.decodeResource(this.getResources(),
				R.drawable.ic_launcher);
		if (canPresentShareDialogWithPhotos) {
			FacebookDialog shareDialog = createShareDialogBuilderForPhoto(image)
					.build();
			uiHelper.trackPendingDialogCall(shareDialog.present());
		} else if (hasPublishPermission()) {
			Request request = Request.newUploadPhotoRequest(
					Session.getActiveSession(), image, new Request.Callback() {
						@Override
						public void onCompleted(Response response) {
							showPublishResult("Post photo",
									response.getGraphObject(),
									response.getError());
						}
					});
			request.executeAsync();
		} else {
			pendingAction = PendingAction.POST_PHOTO;
		}
	}

	private interface GraphObjectWithId extends GraphObject {
		String getId();
	}

	private void showPublishResult(String message, GraphObject result,
			FacebookRequestError error) {
		String title = null;
		String alertMessage = null;
		if (error == null) {
			title = "Success";
			String id = result.cast(GraphObjectWithId.class).getId();
			alertMessage = "Successfully posted.";
		} else {
			title = "Error";
			alertMessage = error.getErrorMessage();
		}

		new AlertDialog.Builder(this).setTitle(title).setMessage(alertMessage)
				.setPositiveButton("OK", null).show();
	}

	private FacebookDialog.PhotoShareDialogBuilder createShareDialogBuilderForPhoto(
			Bitmap... photos) {
		return new FacebookDialog.PhotoShareDialogBuilder(this)
				.addPhotos(Arrays.asList(photos));
	}
}