package com.bizarre.dingdatt;

import java.io.BufferedWriter;
import java.io.File;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.URL;
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
public class ConnectServer extends AsyncTask<String, String, String> {
	public static int MODE_GET = 1;
	public static int MODE_POST = 2;

	String lineEnd = "\r\n";
	String twoHyphens = "--";
	String boundary = "*****";
	String fileName = "";

	Context context;
	ProgressDialog dialog;
	ConnectServerListener listener;
	int mode = -1;
	List<NameValuePair> params = new ArrayList<NameValuePair>();

	int showdialog = 1;

	/**
	 * 
	 * @return
	 */
	public int getShowdialog() {
		return showdialog;
	}

	/**
	 * 
	 * @param showdialog
	 */
	public void setShowdialog(int showdialog) {
		this.showdialog = showdialog;
	}

	/**
	 * 
	 * @param filename
	 */
	public void SendImage(String filename) {

		this.fileName = filename;
	}

	/**
	 * 
	 * @return
	 */
	public List<NameValuePair> getParams() {
		return params;
	}

	/**
	 * 
	 * @param params
	 */
	public void setParams(List<NameValuePair> params) {
		this.params = params;
	}

	/**
	 * 
	 * @return
	 */
	public int getMode() {
		return mode;
	}

	/**
	 * 
	 * @param mode
	 */
	public void setMode(int mode) {
		this.mode = mode;
	}

	/**
	 * 
	 * @return
	 */
	public Context getContext() {
		return context;
	}

	/**
	 * 
	 * @param context
	 */
	public void setContext(Context context) {
		this.context = context;
	}

	/**
	 * 
	 * @param listener
	 */
	public void setListener(ConnectServerListener listener) {
		this.listener = listener;
	}

	/**
	 * 
	 */
	public ConnectServer() {
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

			Log.d("JSON", "JSON DATA " + result);

			if (result.length() != 0) {
				jsonObject = new JSONObject(result);
			}

			listener.onServerResponse(result, jsonObject);

			if (showdialog == 1) {
				if (dialog != null) {
					dialog.dismiss();
				}
			}

		} catch (Exception exp) {

		}
	}

	@Override
	protected String doInBackground(String... params) {

		try {

			Log.d("URL", "URL DATA " + params[0]);

			URL url = new URL(params[0]);
			HttpURLConnection conn = (HttpURLConnection) url.openConnection();
			conn.setReadTimeout(10000 /* milliseconds */);
			conn.setConnectTimeout(15000 /* milliseconds */);

			if (mode == MODE_GET) {

				conn.setRequestMethod("GET");
				conn.setDoInput(true);
				conn.connect();
			} else if (mode == MODE_POST) {

				conn.setRequestMethod("POST");
				conn.setDoOutput(true);

				if (fileName.length() > 0) {
					conn.setRequestProperty("Connection", "Keep-Alive");
					conn.setRequestProperty("ENCTYPE", "multipart/form-data");
					conn.setRequestProperty("Content-Type",
							"multipart/form-data;boundary=" + boundary);
					conn.setRequestProperty("themephoto", fileName);
				}

				conn.connect();

				OutputStream os = conn.getOutputStream();
				BufferedWriter writer = new BufferedWriter(
						new OutputStreamWriter(os, "UTF-8"));

				if (fileName.length() > 0) {

					writer.write(twoHyphens + boundary + lineEnd);
					writer.write("Content-Disposition: form-data; name=\"themephoto\";filename=\""
							+ fileName + "\"" + lineEnd);
				} else {

					writer.write("");
				}

				writer.flush();
				writer.close();
			}

			InputStream stream = conn.getInputStream();

			String data = convertStreamToString(stream);
			stream.close();

			if (data == null)
				return "";
			else
				return data;

		} catch (Exception exp) {
			return "";
		}

	}

	/**
	 * Convert Input stream into String.
	 * 
	 * @param inputstream
	 * @return string
	 */
	static String convertStreamToString(java.io.InputStream inputstream) {
		java.util.Scanner s = new java.util.Scanner(inputstream)
				.useDelimiter("\\A");
		return s.hasNext() ? s.next() : "";
	}
}
