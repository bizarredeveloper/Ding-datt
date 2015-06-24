/*
 * Copyright 2012 The Android Open Source Project
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package com.bizarre.dingdatt.googleplus;

import java.io.IOException;

import android.app.Activity;
import android.app.Dialog;
import android.content.Intent;

import com.bizarre.dingdatt.HomeActivity;
import com.bizarre.dingdatt.googleplus.AbstractGetNameTask.ReturnGooglePlusData;
import com.google.android.gms.auth.GoogleAuthException;
import com.google.android.gms.auth.GoogleAuthUtil;
import com.google.android.gms.auth.GooglePlayServicesAvailabilityException;
import com.google.android.gms.auth.UserRecoverableAuthException;
import com.google.android.gms.common.GooglePlayServicesUtil;

/**
 * This example shows how to fetch tokens if you are creating a foreground
 * task/activity and handle auth exceptions.
 */

public class GetNameInForeground extends AbstractGetNameTask {

	Activity activity;
	static final int REQUEST_CODE_PICK_ACCOUNT = 1000;
	static final int REQUEST_CODE_RECOVER_FROM_AUTH_ERROR = 1001;
	static final int REQUEST_CODE_RECOVER_FROM_PLAY_SERVICES_ERROR = 1002;

	public GetNameInForeground(Activity activity, String email, String scope,
			ReturnGooglePlusData listener) {

		super(activity, email, scope, listener);
		this.activity = activity;
	}

	/**
	 * Get a authentication token if one is not available. If the error is not
	 * recoverable then it displays the error message on parent activity right
	 * away.
	 */
	@Override
	protected String fetchToken() throws IOException {
		try {
			return GoogleAuthUtil.getToken(mActivity, mEmail, mScope);
		} catch (UserRecoverableAuthException userRecoverableException) {
			// GooglePlayServices.apk is either old, disabled, or not present,
			// which is
			// recoverable, so we need to show the user some UI through the
			// activity.

			if (userRecoverableException instanceof GooglePlayServicesAvailabilityException) {
				// The Google Play services APK is old, disabled, or not
				// present.
				// Show a dialog created by Google Play services that allows
				// the user to update the APK
				int statusCode = ((GooglePlayServicesAvailabilityException) userRecoverableException)
						.getConnectionStatusCode();
				Dialog dialog = GooglePlayServicesUtil.getErrorDialog(
						statusCode, activity,
						REQUEST_CODE_RECOVER_FROM_PLAY_SERVICES_ERROR);
				dialog.show();
			} else if (userRecoverableException instanceof UserRecoverableAuthException) {
				// Unable to authenticate, such as when the user has not yet
				// granted
				// the app access to the account, but the user can fix this.
				// Forward the user to an activity in Google Play services.
				Intent intent = ((UserRecoverableAuthException) userRecoverableException)
						.getIntent();
				activity.startActivityForResult(intent,
						REQUEST_CODE_RECOVER_FROM_PLAY_SERVICES_ERROR);

				// startActivityForResult(e.getIntent(), USER_ACCEPTS);
			}

		} catch (GoogleAuthException fatalException) {
			onError("Unrecoverable error " + fatalException.getMessage(),
					fatalException);
		}
		return null;
	}
}
