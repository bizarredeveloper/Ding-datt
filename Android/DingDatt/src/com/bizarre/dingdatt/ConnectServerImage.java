package com.bizarre.dingdatt;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
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
import android.util.Base64;
import android.util.Log;

/**
 * 
 * @author karthik
 *
 */
public class ConnectServerImage extends AsyncTask<String, String, String> {
	public static int MODE_GET = 1;
	public static int MODE_POST = 2;
	String fileName = "";
	String name = "";

	Context context;
	ProgressDialog dialog;
	ConnectServerListener listener;
	int mode = -1;
	ArrayList<String> names = new ArrayList<String>();
	ArrayList<String> values = new ArrayList<String>();

	int showdialog = 1;

	public int getShowdialog() {
		return showdialog;
	}

	public void setShowdialog(int showdialog) {
		this.showdialog = showdialog;
	}

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

	public void SendImage(String name, String filename) {

		this.name = name;
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

	public ConnectServerImage() {
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

		String url = params[0];

		try {
			HttpConnect client = new HttpConnect(url);
			client.connectForMultipart();

			for (int i = 0; i < names.size(); i++) {
				client.addFormPart(names.get(i), values.get(i));
			}

			if (fileName.length() > 0) {

				byte[] baos = null;

				baos = convertFileToByteArray(new File(this.fileName));

				String[] filenames = fileName.split("/");
				String filename = filenames[filenames.length - 1];

				client.addFilePart(name, filename, baos);
			}

			client.finishMultipart();

			data = client.getResponse();

			Log.d("test", data);
		} catch (Throwable t) {
			t.printStackTrace();
		}

		return data;
	}

	/**
	 * Convert file to byte array
	 * 
	 * @param file
	 * @return byte array
	 */
	public static byte[] convertFileToByteArray(File file) {
		byte[] byteArray = null;
		try {
			InputStream inputStream = new FileInputStream(file);
			ByteArrayOutputStream bos = new ByteArrayOutputStream();
			byte[] b = new byte[1024 * 8];
			int bytesRead = 0;

			while ((bytesRead = inputStream.read(b)) != -1) {
				bos.write(b, 0, bytesRead);
			}

			byteArray = bos.toByteArray();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return byteArray;
	}

	/**
	 * Convert Input stream into String.
	 * 
	 * @param inputstream
	 * @return string
	 */
	static String convertStreamToString(java.io.InputStream is) {
		java.util.Scanner s = new java.util.Scanner(is).useDelimiter("\\A");
		return s.hasNext() ? s.next() : "";
	}
}
