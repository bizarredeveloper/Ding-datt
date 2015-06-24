package com.bizarre.dingdatt;

import com.bizarre.dingdatt.strings.LocalData;

import android.app.ActionBar;
import android.app.Activity;
import android.app.SearchManager;
import android.content.Context;
import android.content.Intent;
import android.content.res.Configuration;
import android.os.Bundle;
import android.support.v4.app.ActionBarDrawerToggle;
import android.support.v4.app.FragmentActivity;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.view.Gravity;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.SearchView;
import android.widget.ShareActionProvider;

public class MemberListActivity extends FragmentActivity {
	private DrawerLayout mDrawerLayout;
	private ListView mDrawerList;
	private ActionBarDrawerToggle mDrawerToggle;

	private CharSequence mDrawerTitle;
	private CharSequence mTitle;
	private String[] mPlanetTitles;
	ImageView imageView122;
	MemberListFragment fragment;
	public static Activity memberlist;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_member_list);

		memberlist = this;
		mTitle = mDrawerTitle = getTitle();
		mPlanetTitles = getResources().getStringArray(R.array.menu_list);
		mDrawerLayout = (DrawerLayout) findViewById(R.id.drawer_layout);
		mDrawerList = (ListView) findViewById(R.id.left_drawer);

		// set a custom shadow that overlays the main content when the drawer
		// opens
		mDrawerLayout.setDrawerShadow(R.drawable.drawer_shadow,
				GravityCompat.START);
		// set up the drawer's list view with items and click listener

		Bundle bundle = getIntent().getExtras();

		String group_id = "";
		String groupname = "";
		int owner = 0;

		if (bundle != null) {

			group_id = bundle.getString("groupid");
			groupname = bundle.getString("groupname");
			owner = bundle.getBoolean("owner") == true ? 1 : 0;
		}

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

		fragment = new MemberListFragment(this, group_id, owner);

		if (savedInstanceState == null) {
			// update the main content by replacing fragments
			getSupportFragmentManager().beginTransaction()
					.replace(R.id.content_frame, fragment).commit();
		}

		getActionBar().setDisplayOptions(ActionBar.DISPLAY_SHOW_CUSTOM);
		getActionBar().setCustomView(R.layout.abs_layout);

		imageView122 = (ImageView) getActionBar().getCustomView().findViewById(
				R.id.menudrawer);

		Main.SetTitle(getActionBar().getCustomView(), groupname);

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

		mShareIntent = new Intent();
		mShareIntent.setAction(Intent.ACTION_SEND);
		mShareIntent.setType("text/plain");
		mShareIntent.putExtra(Intent.EXTRA_TEXT, "Test.");
	}

	Intent mShareIntent = null;
	ShareActionProvider mShareActionProvider = null;

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		MenuInflater inflater = getMenuInflater();
		inflater.inflate(R.menu.member_list, menu);

		MenuItem item = menu.findItem(R.id.action_share);

		// Get its ShareActionProvider
		mShareActionProvider = (ShareActionProvider) item.getActionProvider();

		// Connect the dots: give the ShareActionProvider its Share Intent
		if (mShareActionProvider != null) {
			fragment.getShareIntent(mShareActionProvider);
		}

		// Associate searchable configuration with the SearchView
		SearchManager searchManager = (SearchManager) getSystemService(Context.SEARCH_SERVICE);
		SearchView searchView = (SearchView) menu.findItem(
				R.id.action_websearch).getActionView();
		searchView.setSearchableInfo(searchManager
				.getSearchableInfo(getComponentName()));

		SearchView.OnQueryTextListener queryTextListener = new SearchView.OnQueryTextListener() {
			public boolean onQueryTextChange(String newText) {
				// this is your adapter that will be filtered
				// Toast.makeText(GroupListActivity.this, newText,
				// Toast.LENGTH_LONG).show();
				/* listen.onSearchListen(newText); */

				fragment.onSearchGroupMember(newText);
				return true;
			}

			public boolean onQueryTextSubmit(String query) {
				// Toast.makeText(GroupListActivity.this, "Hai - " + query,
				// Toast.LENGTH_LONG).show();
				return true;
				// Here u can get the value "query" which is entered in the
				// search box.

			}
		};
		searchView.setOnQueryTextListener(queryTextListener);

		return super.onCreateOptionsMenu(menu);
	}

	private class DrawerItemClickListener implements
			ListView.OnItemClickListener {
		@Override
		public void onItemClick(AdapterView<?> parent, View view, int position,
				long id) {
			selectItem(position);
		}
	}

	/**
	 * 
	 * @param position
	 */
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
}