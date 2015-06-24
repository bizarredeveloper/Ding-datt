package com.houcine.tumblr.library;

import java.util.List;

import oauth.signpost.commonshttp.CommonsHttpOAuthConsumer;
import oauth.signpost.commonshttp.CommonsHttpOAuthProvider;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.Bundle;
import android.util.Log;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Toast;

import com.tumblr.jumblr.JumblrClient;
import com.tumblr.jumblr.types.Blog;
import com.tumblr.jumblr.types.User;

public class TumblrLoginActivity extends Activity {

	public static final String TAG = "TumblrLogin";

	public static final int TUMBLR_LOGIN_RESULT_CODE_SUCCESS = 233;
	public static final int TUMBLR_LOGIN_RESULT_CODE_FAILURE = 234;

	public static final String TUMBLR_EXTRA_TOKEN = "extra_access_token_tumblr";
	public static final String TUMBLR_EXTRA_TOKEN_SECRET = "extra_access_token_secret_tumblr";

	public static final String TUMBLR_CONSUMER_KEY = "tumblr_consumer_key";
	public static final String TUMBLR_CONSUMER_SECRET = "tumblr_consumer_secret";

	private String blog_name = "";
	
	private WebView tumblrLoginWebView;
	private ProgressDialog mProgressDialog;
	public String tumblrConsumerKey;
	public String tumblrConsumerSecret;
	
	private static JumblrClient client = null;

	private String authURL;

	private SharedPreferences prefs;

	CommonsHttpOAuthConsumer consumer ;//= new CommonsHttpOAuthConsumer(CONSUMER_KEY, CONSUMER_SECRET); 
	CommonsHttpOAuthProvider provider = new CommonsHttpOAuthProvider(
			Constants.REQUEST_TOKEN_URL,
			Constants.ACCESS_TOKEN_URL,
			Constants.AUTH_URL);
	
	private WebViewClient tumblrWebViewClient = new WebViewClient() {
		@Override
		public boolean shouldOverrideUrlLoading(WebView view, String url) {
			Log.d(TAG, "shouldOverrideUrlLoading called, url : "+url);
			if( url.contains(Constants.TUMBLR_CALLBACK_URL)) {
				Log.d(TAG, "tumblr callback url : "+url);
				Uri uri = Uri.parse(url);
				TumblrLoginActivity.this.saveAccessTokenAndFinish(uri);
				return true;
			}
			return false;
		}

		@Override
		public void onPageFinished(WebView view, String url) {
			super.onPageFinished(view, url);
			if(mProgressDialog != null) mProgressDialog.dismiss();
		}

		@Override
		public void onPageStarted(WebView view, String url, Bitmap favicon) {
			super.onPageStarted(view, url, favicon);
			if(mProgressDialog != null) 
				mProgressDialog.show();
		}
	};

	@SuppressLint("SetJavaScriptEnabled")
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_tumblr_login);

		prefs = getSharedPreferences(Constants.PREFERENCE_NAME, Context.MODE_PRIVATE);

		// Get WebView
		tumblrLoginWebView = (WebView)findViewById(R.id.webview_tumblr_login);
		tumblrLoginWebView.getSettings().setJavaScriptEnabled(true);

		// Get consumer key and consumer secret from extras
		Intent intent = getIntent();
		if(intent != null) {
			tumblrConsumerKey = intent.getStringExtra(TUMBLR_CONSUMER_KEY);
			tumblrConsumerSecret = intent.getStringExtra(TUMBLR_CONSUMER_SECRET);
			if(tumblrConsumerKey == null || tumblrConsumerSecret == null) {
				Log.e(Constants.TAG, "ERROR: Consumer Key and Consumer Secret required!");
				TumblrLoginActivity.this.setResult(TUMBLR_LOGIN_RESULT_CODE_FAILURE);
				TumblrLoginActivity.this.finish();
			}
			else {
				consumer = new CommonsHttpOAuthConsumer(tumblrConsumerKey, tumblrConsumerSecret);
				tumblrLoginWebView.setWebViewClient(tumblrWebViewClient);
				Log.d(Constants.TAG, "ASK OAUTH");
				askOAuth();
			}
		}

		mProgressDialog = new ProgressDialog(this);
		mProgressDialog.setMessage(getResources().getString(R.string.please_wait));
		mProgressDialog.setCancelable(false);
		mProgressDialog.setCanceledOnTouchOutside(false);
		mProgressDialog.show();
	}
	
	@Override
	protected void onDestroy() {
		Log.d(TAG, "onDestroy called ");
		super.onDestroy();
		if(mProgressDialog != null)
			mProgressDialog.dismiss();
		mProgressDialog = null;
		
	}

	@Override
	protected void onNewIntent(Intent intent) {
		super.onNewIntent(intent);
	}

	@Override
	protected void onResume() {
		super.onResume();
	}

	private void saveAccessTokenAndFinish(final Uri uri){
		new Thread(new Runnable() {
			@Override
			public void run() {

				try {
					//retrieve token and tokenSecret saved in retrieveRequestToken() call
					String token = prefs.getString(Constants.PREF_KEY_TOKEN, null);
					String tokenSecret = prefs.getString(Constants.PREF_KEY_TOKEN_SECRET, null);
					Log.d(TAG, "token       : "+token);
					Log.d(TAG, "tokenSecret : "+tokenSecret);

					consumer.setTokenWithSecret(token, tokenSecret);

					Log.d(TAG, "consumer.token       : "+consumer.getToken());
					Log.d(TAG, "consumer.tokenSecret : "+consumer.getTokenSecret());

					String oauthToken = uri.getQueryParameter(Constants.IEXTRA_OAUTH_TOKEN);//"oauth_token");
					String oauthVerifier = uri.getQueryParameter(Constants.IEXTRA_OAUTH_VERIFIER);//"oauth_verifier");

					Log.v(TAG, "Token:" +oauthToken);
					Log.v(TAG, "Verifier:" +oauthVerifier);

					provider.retrieveAccessToken(consumer, oauthVerifier);
					Log.d(TAG, "accessToken       retrieveAccessToken    : "+consumer.getToken());
					Log.d(TAG, "accessTokenSecret retrieveAccessToken    : "+consumer.getTokenSecret());

					// save the new access token and tokenSecret retrieved by provider
					SharedPreferences.Editor editor = prefs.edit();
					editor.putString(Constants.PREF_KEY_TOKEN, consumer.getToken());
					editor.putString(Constants.PREF_KEY_TOKEN_SECRET, consumer.getTokenSecret());

					Intent data = new Intent();
					
					// get user informations 
					client = new JumblrClient(tumblrConsumerKey, tumblrConsumerSecret);
					client.setToken(consumer.getToken(), consumer.getTokenSecret());
					User user = client.user();
					if(user != null) {
						Log.d(TAG, "name              : "+user.getName());
						Log.d(TAG, "defaultpostformat : "+user.getDefaultPostFormat());
						Log.d(TAG, "following         : "+user.getFollowingCount());
						Log.d(TAG, "likes             : "+user.getLikeCount());
						Log.d(TAG, "blogs             : "+user.getBlogs().size());
						List<Blog> blogs = user.getBlogs();
						for (Blog blog : blogs) {
							Log.d(TAG, "blogTitle         : "+blog.getTitle());
							Log.d(TAG, "blogAvatar        : "+blog.avatar());
							Log.d(TAG, "blogName          : "+blog.getName());
							blog_name = blog.getName();
							Log.d(TAG, "blogDescription   : "+blog.getDescription());
						}
						
						editor.putString(Constants.PREF_KEY_USER_NAME, user.getName());
						editor.putString(Constants.PREF_KEY_USER_ID, user.getName());
						editor.putString(Constants.PREF_KEY_PICTURE, user.getBlogs().get(0).avatar());
					}
					else 
						Log.d(TAG, "tumblr user is null");

					//send result back to the onActivityResult()
					Log.d("blog", "blog blog blog blog blog blog "
							+ "blog blog blog blog blog blog blog "
							+ "blog blog blog blog blog blog blog "
							+ "blog blog blog blog blog blog blog "
							+ "blog blog blog blog blog blog blog "
							+ "blog blog blog blog blog blog blog "
							+ "blog blog blog blog " + blog_name);
					
					data.putExtra("blog_name", blog_name);
					data.putExtra(TUMBLR_EXTRA_TOKEN, consumer.getToken());
					data.putExtra(TUMBLR_EXTRA_TOKEN_SECRET, consumer.getTokenSecret());
					TumblrLoginActivity.this.setResult(TUMBLR_LOGIN_RESULT_CODE_SUCCESS, data);

					editor.commit();

				} catch (Exception e) {
					e.printStackTrace();
					if(e.getMessage() != null) 
						Log.e(Constants.TAG, e.getMessage());
					else 
						Log.e(Constants.TAG, "ERROR: Tumblr callback failed");

					TumblrLoginActivity.this.setResult(TUMBLR_LOGIN_RESULT_CODE_FAILURE);
				}
				TumblrLoginActivity.this.finish();
			}
		}).start();
	}

	//====== TUMBLR HELPER METHODS ======

	public static boolean isConnected(Context ctx) {
		SharedPreferences sharedPrefs = ctx.getSharedPreferences(Constants.PREFERENCE_NAME, Context.MODE_PRIVATE);
		return sharedPrefs.getString(Constants.PREF_KEY_TOKEN, null) != null;
	}

	public static void logOutOfTumblr(Context ctx){
		SharedPreferences sharedPrefs = ctx.getSharedPreferences(Constants.PREFERENCE_NAME, Context.MODE_PRIVATE);
		Editor e = sharedPrefs.edit();
		e.putString(Constants.PREF_KEY_TOKEN, null); 
		e.putString(Constants.PREF_KEY_TOKEN_SECRET, null); 
		e.remove(Constants.PREF_KEY_ACCESS_TOKEN_INFOS);
		e.remove(Constants.PREF_KEY_USER);
		e.commit();
	}

	public static String getToken(Context ctx){
		SharedPreferences sharedPrefs = ctx.getSharedPreferences(Constants.PREFERENCE_NAME, Context.MODE_PRIVATE);
		return sharedPrefs.getString(Constants.PREF_KEY_TOKEN, null);
	}

	public static String getTokenSecret(Context ctx){
		SharedPreferences sharedPrefs = ctx.getSharedPreferences(Constants.PREFERENCE_NAME, Context.MODE_PRIVATE);
		return sharedPrefs.getString(Constants.PREF_KEY_TOKEN_SECRET, null);
	}

	public static String getProfilPicture(Context ctx){
		SharedPreferences sharedPrefs = ctx.getSharedPreferences(Constants.PREFERENCE_NAME, Context.MODE_PRIVATE);
		return sharedPrefs.getString(Constants.PREF_KEY_PICTURE, null);
	}

	public static String getUsername(Context ctx){
		SharedPreferences sharedPrefs = ctx.getSharedPreferences(Constants.PREFERENCE_NAME, Context.MODE_PRIVATE);
		return sharedPrefs.getString(Constants.PREF_KEY_USER_NAME, null);
	}

	public static String getUserId(Context ctx){
		SharedPreferences sharedPrefs = ctx.getSharedPreferences(Constants.PREFERENCE_NAME, Context.MODE_PRIVATE);
		return sharedPrefs.getString(Constants.PREF_KEY_USER_ID, null);
	}

	public static void saveProfilePicture(Context ctx, String urlPicture){
		SharedPreferences sharedPrefs = ctx.getSharedPreferences(Constants.PREFERENCE_NAME, Context.MODE_PRIVATE);
		Editor e = sharedPrefs.edit();
		e.putString(Constants.PREF_KEY_PICTURE, urlPicture);
		e.commit();
	}


	/**
	 * send RequestToken request to get the authURL 
	 */
	private void askOAuth() {
		new Thread(new Runnable() {
			@Override
			public void run() {
				try {
					authURL = provider.retrieveRequestToken(consumer, Constants.TUMBLR_CALLBACK_URL);
					Log.v(TAG, "Auth url:" + authURL);
					Log.d(TAG, "accessToken       : "+consumer.getToken());
					Log.d(TAG, "accessTokenSecret : "+consumer.getTokenSecret());
					//save tokens in preferences
					SharedPreferences.Editor editor = prefs.edit();
					editor.putString(Constants.PREF_KEY_TOKEN, consumer.getToken());
					editor.putString(Constants.PREF_KEY_TOKEN_SECRET, consumer.getTokenSecret());
					editor.commit();
				} catch (Exception e) {
					final String errorString = e.toString();
					TumblrLoginActivity.this.runOnUiThread(new Runnable() {
						@Override
						public void run() {
							mProgressDialog.cancel();
							Toast.makeText(TumblrLoginActivity.this, errorString.toString(), Toast.LENGTH_SHORT).show();
							finish();
						}
					});
					e.printStackTrace();
					return;
				}

				TumblrLoginActivity.this.runOnUiThread(new Runnable() {
					@Override
					public void run() {
						Log.d(Constants.TAG,"LOADING AUTH URL : "+authURL);
						tumblrLoginWebView.loadUrl(authURL);
					}
				});
			}
		}).start();
	}

	public static JumblrClient getClient(String consumerKey, String consumerSecret) {
		if(client == null)
			client = new JumblrClient(consumerKey, consumerSecret);
		return client;
	}

	public static void setClient(JumblrClient client) {
		TumblrLoginActivity.client = client;
	}
	
	@Override
	public void onBackPressed() {
		// handle the case when the user clic return to cancel the login operation
		Log.d(TAG, "onBackPressed() TumblrLoginActivity");
		logOutOfTumblr(this);
		super.onBackPressed();
	}
	
}
