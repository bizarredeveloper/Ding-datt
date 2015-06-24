package com.bizarre.dingdatt;

import org.json.JSONObject;

/**
 * Interface definition for a callback to be invoked when server response
 * received. This is used for redirect server response to activity
 * 
 * @author karthik
 *
 */
public interface ConnectServerListener {

	/**
	 * Called when server response received.
	 * 
	 * @param sJSON
	 *            - JSON sting
	 * @param jsonObject
	 *            - JSON object
	 */
	public void onServerResponse(String sJSON, JSONObject jsonObject);
}
