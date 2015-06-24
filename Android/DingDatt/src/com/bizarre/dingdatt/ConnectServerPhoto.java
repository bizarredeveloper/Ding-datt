package com.bizarre.dingdatt;

import java.io.ByteArrayOutputStream;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.NameValuePair;
import org.json.JSONObject;

import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Bitmap.CompressFormat;
import android.os.AsyncTask;
import android.util.Log;

/**
 * 
 * @author karthik
 *
 */
public class ConnectServerPhoto extends AsyncTask<String, String, String> {
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
	ArrayList<String> names = new ArrayList<String>();
	ArrayList<String> values = new ArrayList<String>();

	public ArrayList<String> getNames() {
		return names;
	}

	public void setNames(ArrayList<String> names) {
		this.names = names;
	}

	public ArrayList<String> getValues() {
		return values;
	}

	public void setValues(ArrayList<String> values) {
		this.values = values;
	}

	List<NameValuePair> params = new ArrayList<NameValuePair>();

	public void SendImage(String filename) {

		this.fileName = filename;
	}

	public List<NameValuePair> getParams() {
		return params;
	}

	public void setParams(List<NameValuePair> params) {
		this.params = params;
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

	public ConnectServerPhoto() {
		// TODO Auto-generated constructor stub
	}

	@Override
	protected void onPreExecute() {
		// TODO Auto-generated method stub
		super.onPreExecute();

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
			if (dialog != null) {
				dialog.dismiss();
			}
		}
	}

	@Override
	protected String doInBackground(String... params) {

		String data = "";

		String url = params[0];
		String filename = "";

		BitmapFactory.Options options = new BitmapFactory.Options();
		options.inPreferredConfig = Bitmap.Config.ARGB_8888;
		Bitmap bitmap = BitmapFactory.decodeFile(fileName, options);

		if (fileName.length() > 0) {
			String[] filenames = fileName.split("/");
			filename = filenames[filenames.length - 1];
		}

		ByteArrayOutputStream baos = new ByteArrayOutputStream();
		bitmap.compress(CompressFormat.PNG, 0, baos);

		try {
			HttpConnect client = new HttpConnect(url);
			client.connectForMultipart();

			for (int i = 0; i < names.size(); i++) {
				client.addFormPart(names.get(0), values.get(0));
			}

			if (filename.length() > 0) {
				client.addFilePart("themephoto", filename, baos.toByteArray());
			}

			client.finishMultipart();

			data = client.getResponse();

			Log.d("test", data);
		} catch (Throwable t) {
			t.printStackTrace();
		}

		return data;
	}

	static String convertStreamToString(java.io.InputStream is) {
		java.util.Scanner s = new java.util.Scanner(is).useDelimiter("\\A");
		return s.hasNext() ? s.next() : "";
	}
}
