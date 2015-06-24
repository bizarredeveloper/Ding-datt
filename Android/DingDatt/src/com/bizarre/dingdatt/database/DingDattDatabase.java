package com.bizarre.dingdatt.database;

import android.annotation.SuppressLint;
import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteDatabase.CursorFactory;
import android.database.sqlite.SQLiteOpenHelper;

@SuppressLint("SdCardPath")
public class DingDattDatabase extends SQLiteOpenHelper {
	public static String DATABASE = "dingdatt.db";

	public static String DATABASE_NAME = "/data/data/com.bizarre.dingdatt/databases/"
			+ DATABASE;
	public static int DATBASE_VERSION = 1;

	public static String TABLE_GALLERY = "gallery";

	public static String FIELD_GALLERY_GALLERYID = "galleryid";
	public static String FIELD_GALLERY_ID = "contestparticipantid";
	public static String FIELD_GALLERY_USER_ID = "user_id";
	public static String FIELD_GALLERY_MY_USER_ID = "myuser_id";
	public static String FIELD_GALLERY_UPLOADFILE = "uploadfile";
	public static String FIELD_GALLERY_UPLOADDATE = "uploaddate";
	public static String FIELD_GALLERY_UPLOADTOPIC = "uploadtopic";
	public static String FIELD_GALLERY_CONTEST_ID = "contest_id";
	public static String FIELD_GALLERY_VOTINGSTATUS = "votingstatus";
	public static String FIELD_GALLERY_UPLOADEDSTATUS = "uploadedstatus";

	public static String FIELD_GALLERY_PROFILEPICTURE = "profilepicture";
	public static String FIELD_GALLERY_NAME = "name";
	public static String FIELD_GALLERY_FOLLOWING = "following";

	// public static String FIELD_GALLERY_STATUS = "status";
	// if uploaded status is 0 means data not uploaded to server. if 1 means
	// uploaded.

	public DingDattDatabase(Context context) {
		super(context, DATABASE_NAME, null, DATBASE_VERSION);
		// TODO Auto-generated constructor stub
	}

	@Override
	public void onCreate(SQLiteDatabase db) {
		// TODO Auto-generated method stub

		String sQuery = "CREATE TABLE IF NOT EXISTS gallery("
				+ "galleryid INTEGER PRIMARY KEY AUTOINCREMENT"
				+ ", contestparticipantid VARCHAR(20)"
				+ ", contest_id VARCHAR(20)" + ", user_id VARCHAR(20)"
				+ ", uploadfile TEXT" + ", profilepicture TEXT" + ", name TEXT"
				+ ", following INTEGER" + ", uploaddate TEXT"
				+ ", uploadtopic TEXT" + ", myuser_id VARCHAR(20)"
				+ ", votingstatus TEXT DEFAULT ''"
				+ ", uploadedstatus INTEGER DEFAULT 0)";

		db.execSQL(sQuery);
	}

	@Override
	public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
		// TODO Auto-generated method stub

	}

}
