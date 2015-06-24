package com.bizarre.dingdatt;

import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.NameValuePair;
import org.json.JSONObject;

import android.app.ProgressDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

/**
 * 
 * @author karthik
 *
 */
public class ConnectServerParam extends AsyncTask<String, String, String> {
	public static int MODE_GET = 1;
	public static int MODE_POST = 2;

	Context context;
	ProgressDialog dialog;
	ConnectServerListener listener;
	int mode = -1;
	List<NameValuePair> params = new ArrayList<NameValuePair>();
	ArrayList<String> names = new ArrayList<String>();
	ArrayList<String> values = new ArrayList<String>();
	int showdialog = 1;

	public int getShowdialog() {
		return showdialog;
	}

	public void setShowdialog(int showdialog) {
		this.showdialog = showdialog;
	}

	public List<NameValuePair> getParams() {
		return params;
	}

	public int getMode() {
		return mode;
	}

	public void setMode(int mode) {
		this.mode = mode;
	}

	public Context getContext() {
		return context;
	}

	public void setContext(Context context) {
		this.context = context;
	}

	public void setListener(ConnectServerListener listener) {
		this.listener = listener;
	}

	public ConnectServerParam() {
		// TODO Auto-generated constructor stub
	}

	@Override
	protected void onPreExecute() {
		// TODO Auto-generated method stub
		super.onPreExecute();

		if (showdialog == 1)
			dialog = ProgressDialog.show(context, "",
					context.getString(R.string.processing_please_wait));

	}

	@Override
	protected void onPostExecute(String result) {
		// TODO Auto-generated method stub
		super.onPostExecute(result);

		try {
			JSONObject jsonObject = null;

			if (result.length() != 0) {

				jsonObject = new JSONObject(result);
			}

			listener.onServerResponse(result, jsonObject);

		} catch (Exception exp) {

		} finally {
			if (showdialog == 1) {
				if (dialog != null) {
					dialog.dismiss();
				}
			}
		}
	}

	@Override
	protected String doInBackground(String... params) {

		String data = "";
		try {

			if (mode == MODE_POST) {

				HttpConnect client = new HttpConnect(params[0]);
				client.connectForMultipart();

				for (int i = 0; i < names.size(); i++) {
					client.addFormPart(names.get(i), values.get(i));
				}

				client.finishMultipart();
				data = client.getResponse();
			}

			return data;

		} catch (Exception exp) {
			return "";
		}
	}

	public void setParams(ArrayList<String> asName, ArrayList<String> asValue) {

		this.names = asName;
		this.values = asValue;
	}

	private static String getQuery(List<NameValuePair> params)
			throws UnsupportedEncodingException {
		StringBuilder result = new StringBuilder();
		boolean first = true;

		for (NameValuePair pair : params) {
			if (first)
				first = false;
			else
				result.append("&");

			result.append(URLEncoder.encode(pair.getName(), "UTF-8"));
			result.append("=");
			result.append(URLEncoder.encode(pair.getValue(), "UTF-8"));
		}

		return result.toString();
	}

	static String convertStreamToString(java.io.InputStream is) {
		java.util.Scanner s = new java.util.Scanner(is).useDelimiter("\\A");
		return s.hasNext() ? s.next() : "";
	}
}
